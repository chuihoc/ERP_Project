import React from "react";
import { Modal, Input, DatePicker, Tabs, Avatar } from "antd";
import moment from "moment";
import DropDown from "../DropDown/DropDown";
import "./ModalContainer.css";

const duration = [
  { value: 1, label: "1" },
  { value: 5, label: "5" },
  { value: 10, label: "10" },
  { value: 15, label: "15" }
];
const assignedTo = [
  { value: 1, label: "hung.cv1" },
  { value: 2, label: "hung.cv2" },
  { value: 3, label: "hung.cv3" },
  { value: 4, label: "hung.cv4" },
  { value: 5, label: "hung.cv5" }
];

class ModalContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      data:
        props.typeModal === "Edit"
          ? props.dataModal
          : {
              cardId:
                props.titleModal === "New"
                  ? 0
                  : props.titleModal === "In Progress"
                    ? 1
                    : 2,
              text: "",
              start_date: moment(new Date()).format("DD-MM-YYYY"),
              duration: 0,
              assigned: [],
              id: 999,
              due_time: "02-09-2018",
              progress:
                props.titleModal === "New"
                  ? 0
                  : props.titleModal === "In Progress"
                    ? 0.1
                    : 1,
              open: true,
              parent: 1
            },
      currentTab: "1"
    };
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.dataModal !== this.props.dataModal)
      this.setState({ data: nextProps.dataModal });
  }

  handleChangeTitleTag = e => {
    const dataTemp = { ...this.state.data };
    dataTemp.text = e.target.value;
    this.setState({ data: dataTemp });
  };

  handleChangeStartDate = date => {
    const dataTemp = { ...this.state.data };
    dataTemp.start_date = moment(date).format("DD-MM-YYYY");
    this.setState({ data: dataTemp });
  };

  handleSelectAssigned = assigneds => {
    const dataTemp = { ...this.state.data };
    dataTemp.assigned = [...assigneds];
    this.setState({ data: dataTemp });
  };

  handleSelectDuration = duration => {
    const dataTemp = { ...this.state.data };
    dataTemp.duration = duration;
    this.setState({ data: dataTemp });
  };

  handleSaveChangeTag = () => {
    this.props.handleOkTag(this.state.data);
  };
  handleChangeTab = key => {
    this.setState({ currentTab: key });
  };
  render() {
    const { titleModal, visible, handleCloseTag } = this.props;
    const { data } = this.state;
    return (
      <Modal
        title={titleModal}
        centered
        visible={visible}
        onOk={this.handleSaveChangeTag}
        onCancel={handleCloseTag}
        okButtonProps={{ disabled: data.text.length === 0 }}
      >
        <div className="item-content-modal">
          <div className="title-text">
            Title
            <span style={{ color: "red", fontWeight: "normal" }}>&nbsp;*</span>
          </div>
          <Input.TextArea
            placeholder="Enter title project"
            autosize
            value={data.text}
            style={{ width: "100%" }}
            onChange={this.handleChangeTitleTag}
          />
        </div>
        <div className="item-content-modal">
          <div className="title-text">Start date</div>
          <DatePicker
            defaultValue={moment(data.start_date, "DD-MM-YYYY")}
            format="DD-MM-YYYY"
            style={{ width: "100%" }}
            onChange={this.handleChangeStartDate}
          />
        </div>
        <div className="item-content-modal">
          <div className="title-text select-option">Duration</div>
          <DropDown
            dataSelect={duration}
            defaultValue={data.duration}
            style={{ width: "100%" }}
            handleSelect={value => this.handleSelectDuration(value)}
          />
        </div>
        <div className="item-content-modal">
          <div className="title-text">Assigned to</div>
          <DropDown
            isMultiSelect
            isModal
            defaultValue={data.assigned}
            dataSelect={assignedTo}
            handleSelect={assigneds => this.handleSelectAssigned(assigneds)}
          />
        </div>
        <Tabs onChange={this.handleChangeTab}>
          <Tabs.TabPane tab="Comment" key="1">
            {this.state.currentTab === "1" && (
              <div>
                <Avatar icon="user" />
                <Input
                  placeholder="Write a comment"
                  style={{
                    borderRadius: 30,
                    width: "calc(100% - 36px)",
                    marginLeft: 4
                  }}
                />
              </div>
            )}
          </Tabs.TabPane>
          <Tabs.TabPane tab="History" key="2" />
        </Tabs>
      </Modal>
    );
  }
}

export default ModalContainer;
