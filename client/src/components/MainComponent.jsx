import React from "react";
import { connect } from "react-redux";
import { Layout, Menu, Icon, Button, message, Upload } from "antd";
import { Row, Col, Alert } from "antd";
import SidebarMenu from "./SidebarMenu";
import UserManagement from "./Tables/UserManagement";
import LoginForm from "./LoginForm";
import UserInfo from "./UserInfo";
import { getTokenHeader } from "ISD_API";
import { updateStateData } from "actions";
import Loading from "./Loading";
import QuanlyLanguage from "./Tables/QuanlyLanguage";
import QuanlyCanhan from "./Tables/QuanlyCanhan";
import QuanlyOptions from "./Tables/QuanlyOptions";

import QuanlyPhongban from "./Tables/QuanlyPhongban";
import QuanlyKh from "./Tables/QuanlyKh";
import QuanlyNhaCungCap from "./Tables/QuanlyNhaCungCap";
import QuanlyDonDatHang from "./Tables/QuanlyDonDatHang";

import { Route, Redirect, Switch } from "react-router-dom";
import MyTasks from "./Tables/User/MyTasks";

const { Header, Sider, Content } = Layout;

function isLogged() {
  return !!sessionStorage.getItem("ISD_TOKEN");
}

const PUBLIC_ROUTES = ["/login", "/sign-up"];

const PrivateRoute = ({ component: Component, ...rest }) => (
  <Route
    {...rest}
    render={props => {
      return isLogged() ? (
        <Component {...props} />
      ) : (
        <Redirect
          to={{
            pathname: "/login",
            state: { from: props.location }
          }}
        />
      );
    }}
  />
);

class MainComponent extends React.Component {
  state = {
    collapsed: false,
    logged: false,
    loading: true
  };
  toggle = () => {
    this.setState({
      collapsed: !this.state.collapsed
    });
  };
  componentWillMount() {
    let { mainState } = this.props;
    if (!mainState.userInfo) {
      if (isLogged()) {
        fetch(window.ISD_BASE_URL + "fetchRoles", {
          headers: getTokenHeader()
        })
          .then(response => response.json())
          .then(json => {
            if (json.userInfo) {
              let defaultRouter = "";
              if (
                json.scopes[0] &&
                json.scopes[0]["children"] &&
                json.scopes[0]["children"].length
              ) {
                defaultRouter = json.scopes[0]["children"][0].path
                  ? json.scopes[0]["children"][0].path
                  : "";
              }
              this.props.dispatch(
                updateStateData({
                  showLogin: false,
                  userRoles: json.scopes,
                  //defaultRouter: defaultRouter,
                  userInfo: json.userInfo
                })
              );
            } else if (json.status == "error") {
              message.error(json.message, 3);
            }
            this.setState({
              loading: false
            });
          })
          .catch(error => {
            console.warn(error);
            this.setState({
              loading: false
            });
          });
      } else {
        this.setState({
          loading: false
        });
      }
    } else {
      this.props.dispatch(
        updateStateData({
          defaultRouter: mainState.defaultRouter
        })
      );
      this.setState({
        loading: false
      });
    }
    if (mainState.ans_language.length == 0) {
      fetch(window.ISD_BASE_URL + "fetchLang", {
        headers: getTokenHeader()
      })
        .then(response => {
          return response.json();
        })
        .then(json => {
          if (json.status == "success") {
            this.props.dispatch(
              updateStateData({
                ans_language: json.data
              })
            );
          } else if (json.status == "error") {
            message.error(json.message, 3);
          }
          this.setState({
            loading: false
          });
        })
        .catch(error => {
          console.warn(error);
          this.setState({
            loading: false
          });
        });
    }
  }

  renderRouter = () => {
    return (
      <Switch>
        <PrivateRoute path="/qluser" component={UserManagement} />
        <PrivateRoute path="/lang" component={QuanlyLanguage} />
        <PrivateRoute path="/options" component={QuanlyOptions} />
        <PrivateRoute path="/my_info" component={QuanlyCanhan} />
        <PrivateRoute path="/qlpb" component={QuanlyPhongban} />
        <PrivateRoute path="/qlkh" component={QuanlyKh} />
        <PrivateRoute path="/nha_cung_cap" component={QuanlyNhaCungCap} />
        <PrivateRoute path="/request_order" component={QuanlyDonDatHang} />
        <PrivateRoute path="/my_tasks" component={MyTasks} />
        <Redirect to="my_info" />
      </Switch>
    );
  };

  render() {
    if (this.state.loading) {
      return <Loading />;
    }

    let { defaultRouter } = this.props.mainState;
    let { ans_language } = this.props.mainState;

    return (
      <Layout className="layout-wrapper">
        <Sider trigger={null} collapsible collapsed={this.state.collapsed}>
          <div
            style={{ color: "#fff", textAlign: "center", padding: "10px 0" }}
            className="logo"
          >
            <img
              style={{ width: "50%" }}
              src={process.env.PUBLIC_URL + "/images/logoANS.png"}
            />
          </div>
          <SidebarMenu />
        </Sider>
        <Layout>
          <Header style={{ background: "#fff", padding: 0 }}>
            <Icon
              className="trigger"
              type={this.state.collapsed ? "menu-unfold" : "menu-fold"}
              onClick={this.toggle}
            />
            <UserInfo />
          </Header>
          <div
            style={{
              margin: "24px 16px",
              padding: 24,
              background: "#fff",
              minHeight: 280
            }}
          >
            {!defaultRouter ? (
              <Alert
                message={ans_language["sorry_role_not_valid"]}
                type="info"
                showIcon
              />
            ) : (
              this.renderRouter()
            )}
          </div>
        </Layout>
      </Layout>
    );
  }
}

const ConnectMainComponent = connect(state => {
  return {
    mainState: state.main.present
  };
})(MainComponent);

const router = () => {
  return (
    <React.Fragment>
      <Switch>
        <Route path="/login" exact component={LoginForm} />
        <Route path="/" component={ConnectMainComponent} />
      </Switch>
    </React.Fragment>
  );
};

export default router;
