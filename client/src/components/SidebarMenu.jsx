import React from 'react'
import { Menu, Icon, Button } from 'antd';
import {updateStateData} from 'actions';
import {connect} from 'react-redux';
import { Link } from 'react-router-dom';
import { Route } from 'react-router-dom';
const SubMenu = Menu.SubMenu;

class SidebarMenu extends React.Component {

    state = {
        pathname: window.location.pathname
    };

    getMenuItems () {
        let {mainState} = this.props;
        return mainState.userRoles.filter((role) => role.include_in_menu !== false);
    }

  renderMenuItem(history) {
      if (!this.history) {
          this.history = history;
          // this.history.listen(() => {
          //     console.log('change');
          //     this.setState({
          //         pathname: window.location.pathname
          //     });
          // })
      }

    let {mainState} = this.props;
    let {defaultRouter} = mainState;
    let {language} = mainState;
    let menuItems = this.getMenuItems();

    menuItems = menuItems.map((role, index) => {
      if(role.children && role.children.length > 0) {
        return (
          <SubMenu key={role.path} title={
            <span><Icon className="menu_item_left_icon" type={role.icon} />
              {/*ANS_Q: Làm sao chèn biến language vào đây??? */}
              <span>{role.label}</span>
            </span>}> 
             {role.children.map((child) => {
              return (
                <Menu.Item className="menu_item_left"
                  onClick={() => {
                    if(mainState.defaultRouter == child.path) return false;
                    this.props.dispatch(updateStateData({
                      defaultRouter: child.path
                    }));
                  }}
                  key={`/${child.path}`}
                >
                    <Link to={{ pathname: `/${child.path}` }}>
                        {child.icon ?
                            <Icon type={child.icon} />
                            :
                            <Icon type="api" /> }
                        <span>{child.label}</span>
                    </Link>
                </Menu.Item>
              );
            })}
          </SubMenu>
        );
      } else {
        return (
          <Menu.Item
            onClick={() => {
              if(mainState.defaultRouter == role.path) return false;
              this.props.dispatch(updateStateData({
                defaultRouter: role.path
              }));
            }} 
            key={role.path}>
              <Icon type={role.icon} />
              <span>{role.label}</span>
          </Menu.Item>
        );
      }
    });
    return menuItems;
  }
  render() {
    const { pathname } = this.state;
    const defaultOpenKey = (this.getMenuItems().find(role => role.children.find(child => `/${child.path}` === pathname)) || {}).path;
    const defaultSelectKey = pathname;

    if (!this.getMenuItems().length) return null;

    return (
      <div>
          <Route render={({ history }) => (
              <Menu
                  defaultSelectedKeys={[defaultSelectKey]}
                  defaultOpenKeys={[defaultOpenKey]}
                  mode="inline"
                  theme="dark"
              >
                  {this.renderMenuItem(history)}
                  {/* <SubMenu key="user_group" title={<span><Icon type="team" /><span>QL Users</span></span>}>
            <Menu.Item key="5">Option 5</Menu.Item>
          </SubMenu>
          <SubMenu key="setting_group" title={<span><Icon type="setting" /><span>Cài đặt</span></span>}>
            <Menu.Item key="6">Option 6</Menu.Item>
            <Menu.Item key="7">Option 7</Menu.Item>
          </SubMenu>
          <SubMenu key="qlsx_group" title={<span><Icon type="trademark" /><span>QL SX</span></span>}>
            <Menu.Item key="8">Option 8</Menu.Item>
          </SubMenu> */}
              </Menu>
          )} />
      </div>
    );
  }
}

export default connect((state) => {
    return {
        mainState: state.main.present,
    }
})(SidebarMenu);