import React from "react";
import { Tabs } from "antd";
import { connect } from "react-redux";

import { updateData, deleteItem, addItem, editItem } from "../action/viewPage";
import TablePage from "./TablePage";
import DragDrop from "./dndPage/DragDrop";
import ModalContainer from "./Modal/ModalContainer";

class ViewPage extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      visibleModal: false,
      titleModal: "",
      dataModal: null,
      typeModal: "",
      currentTab: "1"
    };
  }

  updateData = (itemId, droppableId) => {
    const dataTemp = this.props.data;
    dataTemp.forEach(item => {
      if (item.id === itemId) {
        if (droppableId === "droppableId1") {
          item.cardId = 0;
        } else if (droppableId === "droppableId2") item.cardId = 1;
        else item.cardId = 2;
      }
    });
    this.props.updateData(dataTemp);
  };

  handleAddTag = droppableId => {
    this.setState({
      visibleModal: true,
      titleModal:
        droppableId === "droppableId1"
          ? "New"
          : droppableId === "droppableId2"
            ? "In Progress"
            : "Completed",
      typeModal: "Add"
    });
  };

  handleEditTag = item => {
    this.setState({
      visibleModal: true,
      titleModal:
        item.cardId === 0
          ? "New"
          : item.cardId === 1
            ? "In Progress"
            : "Completed",
      dataModal: item,
      typeModal: "Edit"
    });
  };

  handleDeleteTag = item => {
    this.props.deleteItem(item);
  };

  handleCloseTag = () => {
    this.setState({
      visibleModal: false,
      titleModal: ""
    });
  };

  handleOkTag = item => {
    if (this.state.typeModal === "Add") {
      item.id = Math.floor(Math.random() * 100000 + 1000);
      this.props.addItem(item);
    } else if (this.state.typeModal === "Edit") {
      const dataTemp = [...this.props.data];
      dataTemp.forEach((itemData, index) => {
        if (itemData.id === item.id) {
          dataTemp[index] = item;
        }
      });
      this.props.editItem(dataTemp);
    }
    this.handleCloseTag();
  };

  handleChangeTab = key => {
    this.setState({ currentTab: key });
  };
  render() {
    const TabPane = Tabs.TabPane;
    const { visibleModal, titleModal, dataModal, typeModal } = this.state;
    return (
      <div style={{ padding: 20 }}>
        <Tabs onChange={this.handleChangeTab}>
          <TabPane tab="Danh sách công việc" key="1">
            {this.state.currentTab === "1" && (
              <DragDrop
                dataSource={this.props.data}
                updateData={this.updateData}
                handleAddTag={this.handleAddTag}
                handleEditTag={this.handleEditTag}
                handleDeleteTag={this.handleDeleteTag}
              />
            )}
          </TabPane>
          <TabPane tab="Hiển thị dạng bảng" key="2">
            {this.state.currentTab === "2" && (
              <TablePage
                dataSource={this.props.data}
                updateData={this.props.updateData}
              />
            )}
          </TabPane>
        </Tabs>
        {visibleModal && (
          <ModalContainer
            titleModal={titleModal}
            visible={visibleModal}
            handleCloseTag={this.handleCloseTag}
            handleOkTag={this.handleOkTag}
            dataModal={dataModal}
            typeModal={typeModal}
          />
        )}
      </div>
    );
  }
}

const mapStateToProps = state => {
  return {
    data: state.trelloReducer.data,
    isFetching: state.trelloReducer.isFetching
  };
};

const mapDispatchToProps = dispatch => ({
  updateData: dataUpdate => {
    dispatch(updateData(dataUpdate));
  },
  deleteItem: item => {
    dispatch(deleteItem(item));
  },
  addItem: item => {
    dispatch(addItem(item));
  },
  editItem: item => {
    dispatch(editItem(item));
  }
});
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ViewPage);
