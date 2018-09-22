import React from "react";
import { updateStateData, updateData } from "actions";
import { message } from "antd";
import { getTokenHeader } from "ISD_API";
import ViewPage from "./../../../vendors/trello/ViewPage/ViewPage";
import { connect } from "react-redux";

class MyTasks extends React.Component {
  constructor(props) {
    super(props);
  }
  fetchData() {
    fetch(window.ISD_BASE_URL + "gantt/fetchMyTasks", {
      headers: getTokenHeader()
    })
      .then(response => response.json())
      .then(json => {
        if (json.data) {
          let data = json.data.map(item => {
            return {
              ...item,
              cardId: 0
            };
          });
          this.props.dispatch(updateData(data));
        } else {
          console.warn(json.message);
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
  componentDidMount() {
    this.fetchData();
  }
  render() {
    return (
      <React.Fragment>
        <ViewPage />
      </React.Fragment>
    );
  }
}

export default connect(state => {
  return {
    trelloReducer: state.trelloReducer
  };
})(MyTasks);
