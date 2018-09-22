import React from "react";
import moment from "moment";
import {
  Table,
  Input,
  InputNumber,
  Select,
  Popconfirm,
  Form,
  Row,
  Col,
  Button,
  message,
  Alert,
  Menu,
  Dropdown,
  Icon,
  DatePicker
} from "antd";
import {
  getTokenHeader,
  convertArrayObjectToObject,
  qcQAStatus
} from "ISD_API";
import { updateStateData } from "actions";

const checkStatusOptions = convertArrayObjectToObject(qcQAStatus);

const FormItem = Form.Item;
const EditableContext = React.createContext();

const EditableRow = ({ form, index, ...props }) => (
  <EditableContext.Provider value={form}>
    <tr {...props} />
  </EditableContext.Provider>
);

const tableConfig = {
  headTitle: "Nguyên liệu",
  addNewTitle: "Thêm"
};

const fetchConfig = {
  fetch: "request_order/fetch",
  update: "request_order/updateProduct",
  delete: "request_order/deleteProduct/",
  changeStatus: "request_order/changeStatus"
};

const EditableFormRow = Form.create()(EditableRow);

class EditableCell extends React.Component {
  getInput = () => {
    switch (this.props.inputType) {
      case "product_id":
        let products = this.props.products;
        return (
          <Select
            showSearch
            optionFilterProp="children"
            onChange={(value, option) => {
              let unit = option.props.children.split("-");
              if (unit && unit[3]) {
                unit = unit[3].trim();
              }
              return false;
            }}
            filterOption={(input, option) =>
              option.props.children
                .toLowerCase()
                .indexOf(input.toLowerCase()) >= 0
            }
            style={{ width: "100%" }}
            placeholder="Chọn VT"
          >
            {products.map(product => {
              return (
                <Select.Option
                  key={product.product_id}
                  value={product.product_id}
                >
                  {`${product.product_id} - ${product.name} - ${product.unit} `}
                </Select.Option>
              );
            })}
          </Select>
        );
      case "qty":
        return (
          <InputNumber
            formatter={value =>
              `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            }
          />
        );
      case "date_delivery":
        return <DatePicker placeholder="Chọn ngày" format="DD/MM/YYYY" />;
      default:
        return <Input />;
    }
  };
  handleChange = value => {
    console.log(value);
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
          let value;
          if (record) {
            value = record[dataIndex];
            if (dataIndex == "date_delivery") {
              value = moment(value);
              if (!value.isValid()) {
                value = null; // Might 	0000-00-00
              }
            }
          }
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
                    initialValue: value
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

class EditableTable extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedRowKeys: [],
      loading: false,
      loadProduct: false
    };
    this.columns = [
      {
        title: "Sản Phẩm",
        dataIndex: "product_id",
        width: "50%",
        editable: true,
        render: (text, record) => {
          //Edit mode
          if (record.product_id && record.name && record.name != "") {
            return <span>{record.product_id + " - " + record.name}</span>;
          }
          let label = text;
          //Add new item
          let productListInfo = convertArrayObjectToObject(
            this.props.mainState.products,
            `product_id`
          );
          if (productListInfo && productListInfo[text]) {
            label = text + " - " + productListInfo[text]["name"];
          }
          return <span>{label}</span>;
        }
      },
      {
        title: "Số lượng",
        dataIndex: "qty",
        width: "100",
        editable: true,
        required: true,
        render: (text, record) =>
          `${text}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      {
        title: "Actions",
        dataIndex: "operation",
        //fixed: 'right',
        width: 100,
        render: (text, record) => {
          let isReadOnly = this.isReadOnly();
          if (isReadOnly) return "";
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
                  <a href="javascript:;" onClick={() => this.edit(record.key)}>
                    Sửa
                  </a>
                  {" | "}
                  <Popconfirm
                    title="Bạn thật sự muốn xoá?"
                    okType="danger"
                    onConfirm={() => this.delete(record)}
                  >
                    <a href="javascript:;">Xoá</a>
                  </Popconfirm>
                </React.Fragment>
              )}
            </div>
          );
        }
      }
    ];
  }
  onSelectChange = selectedRowKeys => {
    this.setState({ selectedRowKeys });
  };
  addNewRow() {
    let { products } = this.props.mainState.request_order || [];
    let { editingKey } = this.props.mainState.requestOrderAction;
    if (editingKey !== undefined && editingKey !== "") return false;
    let rowItem = this.getDefaultFields();
    rowItem = {
      ...rowItem,
      key: products.length + 1
    };

    this.props.dispatch(
      updateStateData({
        request_order: {
          ...this.props.mainState.request_order,
          products: [...products, rowItem]
        },
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          editingKey: rowItem.key
        }
      })
    );
  }
  getDefaultFields() {
    return {
      product_id: "",
      qty: "",
      status: "",
      create_on: ""
    };
  }
  isEditing = record => {
    return record.key === this.props.mainState.requestOrderAction.editingKey;
  };
  isReadOnly() {
    let { requestOrderAction } = this.props.mainState;
    return requestOrderAction && requestOrderAction.action == "view"
      ? true
      : false;
  }
  edit(key) {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          editingKey: key
        }
      })
    );
  }
  save(form, key) {
    form.validateFields((error, row) => {
      if (error) {
        return;
      }
      const newData = [...this.props.mainState.request_order.products];
      const index = newData.findIndex(item => key === item.key);
      if (index > -1) {
        const item = newData[index];
        //console.log(item, row);//update to server here
        let newItemData = {
          ...item,
          ...row
        };
        //Chua co ma phieu va ID la trong
        if (!newData.id) {
          newData.splice(index, 1, {
            ...newItemData
          });
          this.props.dispatch(
            updateStateData({
              request_order: {
                ...this.props.mainState.request_order,
                products: newData
              },
              requestOrderAction: {
                ...this.props.mainState.requestOrderAction,
                editingKey: ""
              }
            })
          );
          return false;
        }
      } else {
        newData.push(row);
        this.setState({ data: newData, editingKey: "" });
      }
    });
  }
  cancel = key => {
    this.props.dispatch(
      updateStateData({
        requestOrderAction: {
          ...this.props.mainState.requestOrderAction,
          editingKey: ""
        }
      })
    );
  };
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
            let newData = this.props.mainState.request_order.products.filter(
              item => item.key != record.id
            );
            this.props.dispatch(
              updateStateData({
                request_order: {
                  ...this.props.mainState.request_order,
                  products: newData
                }
              })
            );
            message.success(json.message);
          }
        })
        .catch(error => {
          message.error("Có lỗi xảy ra khi xoá sản phẩm!", 3);
          console.log(error);
        });
    } else {
      if (record.key) {
        let newData = this.props.mainState.request_order.products.filter(
          item => item.key != record.key
        );
        this.props.dispatch(
          updateStateData({
            request_order: {
              ...this.props.mainState.request_order,
              products: newData
            }
          })
        );
      }
    }
  };
  fetchProduct() {
    fetch(window.ISD_BASE_URL + "request_order/fetchProductDetailsList", {
      headers: getTokenHeader()
    })
      .then(resopnse => resopnse.json())
      .then(json => {
        if (json.data) {
          if (json.data) {
            this.props.dispatch(
              updateStateData({
                products: json.data
              })
            );
          }
        } else {
          message.error(json.message);
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
  fetchSelectedProduct() {
    let { request_order } = this.props.mainState;
    let maPhieu = request_order.ma_order;
    this.setState({ loadProduct: true });
    fetch(
      window.ISD_BASE_URL + `request_order/fetchSelectedProduct/${maPhieu}`,
      {
        headers: getTokenHeader()
      }
    )
      .then(resopnse => resopnse.json())
      .then(json => {
        if (json.data) {
          if (json.data) {
            this.props.dispatch(
              updateStateData({
                request_order: {
                  ...this.props.mainState.request_order,
                  products: json.data
                }
              })
            );
          }
        } else {
          message.error(json.message);
        }
        this.setState({ loadProduct: false });
      })
      .catch(error => {
        console.log(error);
      });
  }
  componentDidMount() {
    let { products, request_order } = this.props.mainState;
    if (!products.length) {
      this.fetchProduct();
    }
    if (request_order.id) {
      this.fetchSelectedProduct();
    }
  }
  render() {
    const components = {
      body: {
        row: EditableFormRow,
        cell: EditableCell
      }
    };
    let products = this.props.mainState.products;
    let columns = this.columns.map(col => {
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
          required: col.required,
          products
        })
      };
    });
    let selectedProducts = this.props.mainState.request_order.products || [];
    const rowSelection = {
      selectedRowKeys: this.state.selectedRowKeys,
      onChange: this.onSelectChange
    };
    return (
      <React.Fragment>
        <div className="table-operations">
          <Row>
            <Col span={12}>
              <h2 className="head-title">{tableConfig.headTitle}</h2>
            </Col>
            <Col span={12}>
              <div className="action-btns">
                {!this.isReadOnly() ? (
                  <Button
                    onClick={() => this.addNewRow()}
                    type="primary"
                    icon="plus"
                  >
                    {tableConfig.addNewTitle}
                  </Button>
                ) : null}
              </div>
            </Col>
          </Row>
        </div>
        <Table
          rowSelection={rowSelection}
          components={components}
          bordered
          dataSource={selectedProducts}
          columns={columns}
          rowClassName="editable-row"
          loading={this.state.loadProduct}
        />
      </React.Fragment>
    );
  }
}

export default EditableTable;
