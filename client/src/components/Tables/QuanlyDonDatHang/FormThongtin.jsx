import React from "react";
import moment from "moment";
import {
  Form,
  Select,
  Input,
  Row,
  Col,
  DatePicker,
  Radio,
  Button,
  Popconfirm,
  message
} from "antd";
import { updateStateData } from "actions";
import { getTokenHeader, trangThaiPhieu } from "ISD_API";
import UploadFile from "./../../UploadFile";
const FormItem = Form.Item;
const Option = Select.Option;
const dateFormat = "YYYY/MM/DD";
const RadioGroup = Radio.Group;

const formInfo = {
  person: "Người giao hàng"
};

class FormThongtin extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      customers: []
    };
  }
  fetchData() {
    fetch(window.ISD_BASE_URL + "request_order/fetch", {
      headers: getTokenHeader()
    })
      .then(response => {
        return response.json();
      })
      .then(json => {
        if (json.status == "error") {
          message.warning(json.message, 3);
        } else {
          if (json.data) {
            this.props.dispatch(updateStateData({ kho: json.data }));
          }
        }
      })
      .catch(error => {
        message.error("Có lỗi khi tải dữ liệu dữ liệu kho!", 3);
        console.log(error);
      });
  }
  fetchCustomers() {
    fetch(window.ISD_BASE_URL + "qlkh/fetchKh", {
      headers: getTokenHeader()
    })
      .then(response => response.json())
      .then(json => {
        if (json.data) {
          this.setState({
            customers: json.data
          });
        } else {
          console.warn(json.message);
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
  componentDidMount() {
    let customers = this.state.customers;
    if (customers.length == 0) {
      this.fetchCustomers();
    }
  }
  render() {
    let {
      request_order,
      requestOrderAction,
      ans_language
    } = this.props.mainState;
    let customerOptions = [];
    if (this.state.customers && this.state.customers.length) {
      customerOptions = this.state.customers.map(user => {
        return (
          <Option value={user.ma_kh} key={user.ma_kh}>
            {user.ma_kh} - {user.name}
          </Option>
        );
      });
    }

    let date_delivery = request_order.date_delivery;
    date_delivery = moment(date_delivery);
    if (!date_delivery.isValid()) {
      date_delivery = null; // Might 	0000-00-00
    }
    let readOnly =
      requestOrderAction && requestOrderAction.action == "view" ? true : false;
    //File upload
    let attachFile;
    if (request_order.filename && request_order.filename != "") {
      attachFile = request_order.filename.split(",").map(file => {
        return {
          uid: file,
          name: file,
          status: "done",
          url: window.ISD_BASE_URL + "upload/" + file
        };
      });
    }
    return (
      <Form>
        <Row>
          <Col span={12}>
            <FormItem
              label={ans_language.ans_order_titles || "ans_order_titles"}
              labelCol={{ span: 5 }}
              wrapperCol={{ span: 12 }}
            >
              <Input
                readOnly={readOnly}
                onChange={e => {
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        ma_order: e.target.value
                      }
                    })
                  );
                }}
                value={request_order.ma_order}
              />
            </FormItem>
          </Col>
          <Col span={12}>
            <FormItem
              label={ans_language.ans_customer_titles || "ans_customer_titles"}
              labelCol={{ span: 5 }}
              wrapperCol={{ span: 12 }}
            >
              <Select
                placeholder="Chọn khách hàng"
                value={request_order.ma_kh}
                onChange={value => {
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        ma_kh: value
                      }
                    })
                  );
                }}
              >
                {customerOptions}
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <FormItem
              label={"GHI CHÚ"}
              labelCol={{ span: 24 }}
              wrapperCol={{ span: 24 }}
            >
              <Input.TextArea
                rows={4}
                readOnly={readOnly}
                onChange={e => {
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        note: e.target.value
                      }
                    })
                  );
                }}
                placeholder="Hãy nhập ghi chú ở đây"
                autosize={{ minRows: 6 }}
                defaultValue={request_order.note}
              />
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span={12}>
            <FormItem
              label={
                ans_language.ans_attach_file_label || "ans_attach_file_label"
              }
              labelCol={{ span: 5 }}
              wrapperCol={{ span: 12 }}
            >
              <UploadFile
                fileList={attachFile ? attachFile : []}
                onDone={filename => {
                  let files = this.props.mainState.request_order.filename || "";
                  if (files != "") {
                    files += `,${filename}`;
                  } else {
                    files = filename;
                  }
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        filename: files
                      }
                    })
                  );
                }}
                onRemove={filename => {
                  let files = this.props.mainState.request_order.filename || "";
                  if (files != "") {
                    files = files.split(",").filter(file => file != filename);
                    files = files.join(",");
                  }
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        filename: files
                      }
                    })
                  );
                }}
                mainState={this.props.mainState}
                dispatch={this.props.dispatch}
              />
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem
              label={ans_language.ans_date_delive || "ans_date_delive"}
              labelCol={{ span: 5 }}
              wrapperCol={{ span: 12 }}
            >
              <DatePicker
                onChange={(date, dateString) => {
                  let formatDate = "";
                  if (date) {
                    formatDate = date.format("YYYY-MM-DD");
                  }
                  this.props.dispatch(
                    updateStateData({
                      request_order: {
                        ...this.props.mainState.request_order,
                        date_delivery: formatDate
                      }
                    })
                  );
                }}
                defaultValue={date_delivery}
                placeholder="Chọn ngày"
                format="DD/MM/YYYY"
              />
            </FormItem>
          </Col>
        </Row>
      </Form>
    );
  }
}

export default FormThongtin;
