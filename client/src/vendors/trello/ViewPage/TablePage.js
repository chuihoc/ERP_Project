import React from "react";
import { Table } from "antd";
import DropDown from "./DropDown/DropDown";

const dataAssigned = [
  { value: 1, label: "hung.cv1" },
  { value: 2, label: "hung.cv2" },
  { value: 3, label: "hung.cv3" },
  { value: 4, label: "hung.cv4" },
  { value: 5, label: "hung.cv5" }
];
let activePage = 0;

class TablePage extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      dataSource: props.dataSource
    };
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.dataSource !== this.props.dataSource)
      this.setState({ dataSource: nextProps.dataSource });
  }
  getStatus = cardId => {
    if (cardId === 0) return <span style={{ color: "#00B0FF" }}>New</span>;
    if (cardId === 1)
      return <span style={{ color: "#00E5FF" }}>In Progress</span>;
    if (cardId === 2) return <span style={{ color: "#9CCC65" }}>Complete</span>;
  };

  handleChange = (pagination, filters, sorter) => {
    activePage = pagination.current - 1;
    if (filters.cardId) {
      if (filters.cardId.length > 0) {
        const dataTable = [...this.props.dataSource];
        const result = dataTable.filter(item =>
          filters.cardId.includes(item.cardId.toString())
        );
        this.setState({ dataSource: result });
      } else {
        this.setState({ dataSource: this.props.dataSource });
      }
    }
  };
  handleSelect = (item, data, index) => {
    const tempData = [...this.state.dataSource];
    tempData[index].assigned = [...data];
    // this.setState({ dataSource: tempData });
    this.props.updateData(tempData);
  };

  render() {
    const { dataSource } = this.state;
    const columns = [
      {
        title: "",
        width: 54,
        render: (text, item, index) => index + 1 + activePage * 10,
        key: "key"
      },
      {
        title: "Title",
        dataIndex: "text",
        key: "text"
      },
      {
        title: "Start",
        dataIndex: "start_date",
        key: "start_date"
      },
      {
        title: "Due",
        dataIndex: "due_time",
        render: (text, item) => (
          <span style={{ color: item.cardId === 1 && "red" }}>{text}</span>
        ),
        key: "due_time"
      },
      {
        title: "Duration",
        dataIndex: "duration",
        key: "duration"
      },
      {
        title: "Progress",
        dataIndex: "progress",
        render: (text, item) => <span>{item.progress * 100}%</span>,
        key: "progress"
      },
      {
        title: "Status",
        dataIndex: "cardId",
        render: cardId => <span>{this.getStatus(cardId)}</span>,
        key: "cardId",
        filters: [
          { text: "New", value: 0 },
          { text: "In Progress", value: 1 },
          { text: "Complete", value: 2 }
        ]
      },
      {
        title: "Assigned to",
        dataIndex: "assigned",
        width: 250,
        render: (text, item, index) => (
          <DropDown
            key={item.id}
            rowKey={item => item.id}
            isMultiSelect
            dataSelect={dataAssigned}
            defaultValue={item.assigned}
            handleSelect={data =>
              this.handleSelect(item, data, index + activePage * 10)
            }
          />
        ),
        key: "assign"
      }
    ];
    return (
      <Table
        bordered
        rowKey={record => record.id}
        dataSource={dataSource}
        columns={columns}
        onChange={this.handleChange}
      />
    );
  }
}

export default TablePage;
