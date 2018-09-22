import React from 'react'
import { Button } from 'antd';
import {updateStateData} from 'actions'
import {connect} from 'react-redux';
import { Route } from 'react-router-dom'

class UserInfo extends React.Component {
  showUserName() {
    let {userInfo} = this.props.mainState;
    if(userInfo) {
      return userInfo.name || userInfo.username || userInfo.email;
    }
  }

  logOUt (history) {
      sessionStorage.removeItem('ISD_TOKEN');
       history.push('/login');
  }

  render() {
    let {ans_language} = this.props.mainState;
    return (
      <div className="admin-user-info">
        <span>{ans_language.ans_xin_chao || "Xin chào" } <b>{this.showUserName()}</b></span>
        {/* <Button 
          ghost
          style={{marginRight: 10}}
          //onClick={() => this.addNewRow()}
          type="primary" icon="mail">Góp ý, phản hồi</Button> */}
          <Route render={({ history }) => (
              <Button
                  onClick={this.logOUt.bind(this, history)}
                  type="primary" ghost>{ans_language.ans_dang_xuat || "Đăng xuất" }
              </Button>
          )} />
      </div>
    );
  }
}

export default connect((state) => {
    return {
        mainState: state.main.present,
    }
})(UserInfo);