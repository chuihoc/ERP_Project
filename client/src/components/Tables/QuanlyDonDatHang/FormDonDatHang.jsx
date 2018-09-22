import React from "react";
import { connect } from "react-redux";
import FormThongTin from "./FormThongtin";
import FormSanpham from "./FormSanpham";
import { Row, Button, Col, Popconfirm, message, Select } from "antd";
import { getTokenHeader, blankGanttData } from "ISD_API";
import { updateStateData } from "actions";

class FormDonDatHang extends React.Component {
  constructor(props) {
    super(props);
  }
  handleSubmit = e => {
    e.preventDefault();
    let isValid = this.validBeforeSave();
    if (isValid) {
      fetch(window.ISD_BASE_URL + "request_order/update", {
        method: "POST",
        headers: getTokenHeader(),
        body: JSON.stringify(this.props.mainState.request_order)
      })
        .then(response => {
          return response.json();
        })
        .then(json => {
          if (json.status == "error") {
            message.error(json.message, 3);
            if (json.show_login) {
              this.props.dispatch(updateStateData({ showLogin: true }));
            }
          } else {
            message.success(json.message);
            this.props.dispatch(
              updateStateData({
                request_order: {
                  refresh: true
                },
                requestOrderAction: {}
              })
            );
          }
        })
        .catch(ex => {
          console.log("parsing failed", ex);
          message.error("Có lỗi xảy ra trong quá trình lưu hoặc chỉnh sửa!");
        });
    }
  };
  validBeforeSave() {
    let { request_order } = this.props.mainState;
    if (!request_order.ma_order) {
      message.error("Mã đơn hàng không được để trống");
      return false;
    }
    if (request_order.products && !request_order.products.length) {
      message.error("Chưa có sản phẩm nào trong đơn hàng!");
      return false;
    }

    return true;
  }
  cancel() {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          addNewItem: false
        }
      })
    );
  }

  render() {
    let {
      requestOrderAction,
      quyTrinhTheoLenh,
      quyTrinhSx,
      ganttData
    } = this.props.mainState;
    return (
      <div>
        <React.Fragment>
          <div className="table-operations">
            <Row>
              <Col span={12}>
                <h2 className="head-title">Thông tin đơn hàng</h2>
              </Col>
              <Col span={12}>
                <div className="action-btns">
                  {requestOrderAction && requestOrderAction.action == "edit" ? (
                    <React.Fragment>
                      <Button
                        onClick={this.handleSubmit}
                        type="primary"
                        htmlType="button"
                        icon="save"
                      >
                        Lưu
                      </Button>
                      <Popconfirm
                        title="Bạn thật sự muốn huỷ?"
                        onConfirm={() => this.cancel()}
                      >
                        <Button style={{ marginLeft: 10 }} type="danger">
                          Huỷ
                        </Button>
                      </Popconfirm>
                    </React.Fragment>
                  ) : (
                    <React.Fragment>
                      <Button
                        onClick={() => {
                          this.props.dispatch(
                            updateStateData({
                              requestOrderAction: {
                                ...this.props.mainState.requestOrderAction,
                                action: "edit"
                              }
                            })
                          );
                        }}
                        type="primary"
                        htmlType="button"
                        icon="edit"
                      >
                        Sửa
                      </Button>
                      <Button
                        onClick={() => this.cancel()}
                        style={{ marginLeft: 10 }}
                        icon="left"
                        type="default"
                      >
                        Quay lại
                      </Button>
                    </React.Fragment>
                  )}
                </div>
              </Col>
            </Row>
          </div>
          <FormThongTin
            dispatch={this.props.dispatch}
            mainState={this.props.mainState}
          />
          <FormSanpham
            dispatch={this.props.dispatch}
            mainState={this.props.mainState}
          />
        </React.Fragment>
      </div>
    );
  }
}

export default connect(state => {
  return {
    mainState: state.main.present
  };
})(FormDonDatHang);
