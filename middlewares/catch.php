<?php

$ISD_KEY = '';
$PROTECTED_PATHS = '';
if(defined('ISD_APP_KEY')) {
  $ISD_KEY = ISD_APP_KEY;
}
if(defined('PROTECTED_PATHS')) {
  $PROTECTED_PATHS = PROTECTED_PATHS;
}
$app->add(new \Slim\Middleware\JwtAuthentication([
    "rules" => [
        new \Slim\Middleware\JwtAuthentication\RequestPathRule([
            "path" => $PROTECTED_PATHS,
            "passthrough" => ["/login", "/token"]
        ]),
        new \Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "passthrough" => ["/login"]
        ]),
        new Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "ignore" => ["OPTIONS"]
        ])
    ],
    "secure" => true,//Should use HTTPS request
    "relaxed" => ["localhost", "127.0.0.1", "erpapp", "annhien", "lotus", "erp"],
    "secret" => $ISD_KEY,
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["jwt"] = $arguments["decoded"];
        $isSuperAdmin = $container['UserController']->isSuperAdmin();
        if($isSuperAdmin) return true;
        //Check first login witout token, check user permisstion and current router
        $route = $request->getAttribute('route');
        if($route) {
          $name = $route->getName();
          if($name != "" && $name != "token") {
            //Check permission of this router
            $userId = $container->jwt->id ? : "";
            if($userId) {
              $isAllow = false;
              $permission = $container['UserController']->getUserPermission($userId);
              if(!empty($permission)) {
                //Check current router doing add or edit entry
                if(strpos($name, '__add') || strpos($name, '__edit')) {
                  //Check param has ID or not 
                  $id = $request->getParam("id");
                  if($id != "") {
                    //This is a update request 
                    $name = str_replace('__add', '__edit', $name);
                  }
                }
                $allowedList = [];
                // echo "<pre>";
                // print_r($permission);
                // echo $name;
                foreach ($permission as $key => $router) {
                  $allowedList[] = $router['router_name'];
                  if(in_array($name, $allowedList)) {
                    return true;
                  }
                }
              }
              return false;
            }
          }
        }
    },
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["show_login"] = true;
        $data["message"] = "Bạn không có quyền để thực hiện tác vụ này";//$arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
  ]));
// Catch all http errors here.
// $app->add(function ($request, $response, $next) use ($container) {

//     // Default status code.
//     $status = 200;

//     // Catch errors.
//     try {
//         $response = $next($request, $response);
//         $status = $response->getStatusCode();

//         // If it is 404, throw error here.
//         if ($status === 404) {
//             throw new \Exception('Page not found', 404);

//             // A 404 should be invoked.
//             // Note since it is to be taken care by the exception below
//             // so comment this custom 404.
//             // $handler = $container->get('notFoundHandler');
//             // return $handler($request, $response);
//         }
//     } catch (\Exception $error) {
//         $status = $error->getCode();
//         $data = [
//             "status" => $error->getCode(),
//             "messsage" => $error->getMessage()
//         ];
//         $response->getBody()->write(json_encode($data));
//     };

//     return $response
//         ->withStatus($status);
//         //->withHeader('Content-type', 'application/json');
// });

// Sample.
// $app->add(function ($request, $response, $next) {
//     $response->getBody()->write('Check permission');
//     $response = $next($request, $response);
//     $response->getBody()->write('Say hi to every one');

//     return $response;
// });
