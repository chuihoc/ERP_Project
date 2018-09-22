import React from "react";
import {
  Table,
  Input,
  Select,
  Icon,
  Popconfirm,
  Form,
  Row,
  Col,
  Button,
  message,
  Badge
} from "antd";
import {
  getTokenHeader,
  convertArrayObjectToObject,
  trangThaiPhieu,
  blankGanttData
} from "./../../ISD_API";
import { updateStateData } from "actions";
import FormDonDatHang from "./QuanlyDonDatHang/FormDonDatHang";
import { connect } from "react-redux";
import moment from "moment";
import QuytrinhDonDatHang from "./QuanlyDonDatHang/QuytrinhDonDatHang";
const trangThaiPhieuObj = convertArrayObjectToObject(trangThaiPhieu);
const FormItem = Form.Item;
const EditableContext = React.createContext();

const EditableRow = ({ form, index, ...props }) => (
  <EditableContext.Provider value={form}>
    <tr {...props} />
  </EditableContext.Provider>
);

const tableConfig = {
  headTitle: "Quản lý đơn đặt hàng",
  addNewTitle: "Thêm đơn hàng"
};

const fetchConfig = {
  fetch: "request_order/fetch",
  delete: "request_order/delete/"
};

const EditableFormRow = Form.create()(EditableRow);

class EditableCell extends React.Component {
  getInput = () => {
    switch (this.props.inputType) {
      default:
        return <Input />;
    }
  };
  render() {
    const {
      editing,
      required,
      dataIndex,
      title,
      inputType,
      record,
      index,
      ...restProps
    } = this.props;
    return (
      <EditableContext.Consumer>
        {form => {
          const { getFieldDecorator } = form;
          return (
            <td {...restProps}>
              {editing ? (
                <FormItem style={{ margin: 0 }}>
                  {getFieldDecorator(dataIndex, {
                    rules: [
                      {
                        required: required,
                        message: `Hãy nhập dữ liệu ô ${title}!`
                      }
                    ],
                    initialValue: record[dataIndex]
                  })(this.getInput())}
                </FormItem>
              ) : (
                restProps.children
              )}
            </td>
          );
        }}
      </EditableContext.Consumer>
    );
  }
}

class QuanlyDonDatHang extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      data: [],
      addNewItem: false
    };
    this.columns = [
      {
        title: "Mã đơn hàng",
        dataIndex: "ma_order",
        //width: '15%',
        editable: false
      },
      {
        title: "Mã KH",
        dataIndex: "ma_kh",
        //width: '40%',
        editable: false
      },
      {
        title: "Ngày giao hàng",
        dataIndex: "date_delivery",
        //width: '40%',
        editable: false,
        render: text => (text ? moment(text).format("DD/MM/YYYY") : "")
      },
      {
        title: "Tình trạng",
        dataIndex: "tinh_trang",
        //width: '40%',
        editable: false,
        render: (text, record) => {
          switch (text) {
            case "1":
              return <span>Đang xử lý</span>;
            default:
              return <span>Đang chờ</span>;
          }
        }
      },
      {
        title: "Quy trình",
        dataIndex: "quy_trinh",
        //width: '40%',
        editable: false,
        render: (text, record) => {
          return (
            <div>
              <Icon type="share-alt" theme="outlined" />{" "}
              <a
                href="javascript:;"
                onClick={() => this.viewProgress(record)}
                style={{ marginRight: 8 }}
              >
                Quy trình
              </a>
            </div>
          );
        }
      },
      {
        title: "Actions",
        dataIndex: "operation",
        render: (text, record) => {
          const editable = this.isEditing(record);
          return (
            <div style={{ minWidth: 100 }}>
              {editable ? (
                <span>
                  <EditableContext.Consumer>
                    {form => (
                      <a
                        href="javascript:;"
                        onClick={() => this.save(form, record.key)}
                        style={{ marginRight: 8 }}
                      >
                        Lưu
                      </a>
                    )}
                  </EditableContext.Consumer>
                  <Popconfirm
                    title="Bạn thật sự muốn huỷ?"
                    onConfirm={() => this.cancel(record.key)}
                  >
                    <a href="javascript:;">Huỷ</a>
                  </Popconfirm>
                </span>
              ) : (
                <React.Fragment>
                  <a href="javascript:;" onClick={() => this.view(record)}>
                    <Icon type="profile" /> Chi tiết
                  </a>
                  {
                    <React.Fragment>
                      {" | "}
                      <Popconfirm
                        title="Bạn thật sự muốn xoá?"
                        okType="danger"
                        onConfirm={() => this.delete(record)}
                      >
                        <a href="javascript:;">Xoá</a>
                      </Popconfirm>
                    </React.Fragment>
                  }
                </React.Fragment>
              )}
            </div>
          );
        }
      }
    ];
  }
  getDefaultFields() {
    return {
      id: "",
      ma_order: "",
      ma_kh: "",
      date_delivery: "",
      note: "",
      tinh_trang: "",
      filename: "",
      products: []
    };
  }
  addNewRow() {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          addNewItem: true,
          action: "edit",
          editingKey: ""
        },
        request_order: this.getDefaultFields()
      })
    );
  }
  isEditing = record => {
    return record.key === this.state.editingKey;
  };
  viewProgress(record) {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          addNewItem: false,
          showProgress: true
        },
        request_order: {
          ma_order: record.ma_order
        }
      })
    );
  }
  view(phieu) {
    let { request_order, requestOrderAction } = this.props.mainState;
    if (phieu && phieu.id) {
      this.props.dispatch(
        updateStateData({
          request_order: {
            ...request_order,
            ...phieu
          },
          requestOrderAction: {
            ...requestOrderAction,
            addNewItem: true,
            action: "view"
          },
          ganttData: blankGanttData
        })
      );
    }
  }
  fetchData() {
    fetch(window.ISD_BASE_URL + fetchConfig.fetch, {
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
            //Add key prop for table
            let data = json.data.map((item, index) => ({
              ...item,
              key: index
            }));
            this.setState({
              data,
              dataUpToDate: true
            });
            //Stop after fetching data
            this.props.dispatch(
              updateStateData({
                request_order: {
                  ...this.props.mainState.request_order,
                  refresh: false
                }
              })
            );
          }
        }
      })
      .catch(error => {
        message.error("Có lỗi khi tải dữ liệu sản phẩm!", 3);
        console.log(error);
      });
  }
  fetchQuyTrinhMau() {
    fetch(window.ISD_BASE_URL + "quytrinhsx/fetch", {
      headers: getTokenHeader()
    })
      .then(response => {
        return response.json();
      })
      .then(json => {
        if (json.status == "error") {
          message.warning(json.message, 3);
          if (json.show_login) {
            this.props.dispatch(updateStateData({ showLogin: true }));
          }
        } else {
          if (json.data) {
            this.props.dispatch(
              updateStateData({
                quyTrinhSx: {
                  ...this.props.mainState.quyTrinhSx,
                  listQuyTrinh: json.data
                }
              })
            );
          }
        }
      })
      .catch(error => {
        message.error("Có lỗi khi tải dữ liệu quy trình sản xuất!", 3);
        console.log(error);
      });
  }
  delete = record => {
    if (record.id) {
      fetch(window.ISD_BASE_URL + fetchConfig.delete + record.id, {
        headers: getTokenHeader()
      })
        .then(response => response.json())
        .then(json => {
          if (json.status == "error") {
            message.error("Có lỗi xảy ra khi xoá sản phẩm!", 3);
          } else {
            let newData = this.state.data.filter(item => item.id != json.data);
            this.setState({ data: newData });
            message.success(json.message);
          }
        })
        .catch(error => {
          message.error("Có lỗi xảy ra khi xoá sản phẩm!", 3);
          console.log(error);
        });
    } else {
      if (record.key) {
        let newData = this.state.data.filter(item => item.key != record.key);
        this.setState({
          data: newData
        });
      }
    }
  };
  static getDerivedStateFromProps(nextProps, prevState) {
    let { refresh } = nextProps.mainState.request_order;
    if (refresh) {
      return {
        dataUpToDate: null
      };
    }
    return null;
  }
  componentDidUpdate() {
    if (this.state.dataUpToDate === null) {
      this.fetchData();
    }
  }
  componentDidMount() {
    this.fetchData();
    this.fetchQuyTrinhMau();
  }
  render() {
    let { mainState } = this.props;
    const components = {
      body: {
        row: EditableFormRow,
        cell: EditableCell
      }
    };

    const columns = this.columns.map(col => {
      if (!col.editable) {
        return col;
      }
      return {
        ...col,
        onCell: record => ({
          record,
          inputType: col.dataIndex,
          dataIndex: col.dataIndex,
          title: col.title,
          editing: this.isEditing(record),
          required: col.required
        })
      };
    });

    return (
      <React.Fragment>
        {mainState.requestOrderAction.addNewItem ? (
          <FormDonDatHang
            dispatch={this.props.dispatch}
            mainState={this.props.mainState}
          />
        ) : (
          <React.Fragment>
            {mainState.requestOrderAction.showProgress ? (
              <QuytrinhDonDatHang
                dispatch={this.props.dispatch}
                mainState={this.props.mainState}
              />
            ) : (
              <React.Fragment>
                <div className="table-operations">
                  <Row>
                    <Col span={12}>
                      <h2 className="head-title">{tableConfig.headTitle}</h2>
                    </Col>
                    <Col span={12}>
                      <div className="action-btns">
                        {
                          <Button
                            onClick={() => this.addNewRow()}
                            type="primary"
                            icon="plus"
                          >
                            {tableConfig.addNewTitle}
                          </Button>
                        }
                      </div>
                    </Col>
                  </Row>
                </div>
                <Table
                  components={components}
                  bordered
                  dataSource={this.state.data}
                  columns={columns}
                  rowClassName="editable-row"
                />
              </React.Fragment>
            )}
          </React.Fragment>
        )}
      </React.Fragment>
    );
  }
}

export default connect(state => {
  return {
    mainState: state.main.present
  };
})(QuanlyDonDatHang);
