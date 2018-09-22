import React from "react";
import { Row, Button, Col, Popconfirm, message, Select } from "antd";
import { updateStateData } from "actions";
import Gantt from "./../QuanlyQuytrinhSanxuat/Gantt";

class QuytrinhDonDatHang extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }
  cancel() {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          showProgress: false
        }
      })
    );
  }
  componentDidMount() {}
  render() {
    let { requestOrderAction, request_order } = this.props.mainState;
    return (
      <React.Fragment>
        <div className="table-operations">
          <Row>
            <Col span={12}>
              <h2 className="head-title">
                Quy trình thực hiện đơn hàng: {request_order.ma_order}
              </h2>
            </Col>
            <Col span={12}>
              <div className="action-btns">
                <Button
                  onClick={() => this.cancel()}
                  style={{ marginLeft: 10 }}
                  icon="left"
                  type="default"
                >
                  Quay lại
                </Button>
              </div>
            </Col>
          </Row>
        </div>
        <div className="wrap-gantt-chart">
          <Gantt
            type="theo_don_hang"
            dispatch={this.props.dispatch}
            mainState={this.props.mainState}
          />
        </div>
      </React.Fragment>
    );
  }
}

export default QuytrinhDonDatHang;
