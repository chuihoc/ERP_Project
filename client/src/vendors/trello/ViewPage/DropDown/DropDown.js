import React from "react";
import { Select } from "antd";

class DropDown extends React.Component {
  handleChange = value => {
    this.props.handleSelect(value);
  };

  render() {
    const { dataSelect, defaultValue, isMultiSelect, isModal } = this.props;
    const styleSelect = isMultiSelect
      ? { width: "100%", maxWidth: !isModal && 250 }
      : { width: "100%" };
    return (
      <Select
        key={"uid"}
        defaultValue={defaultValue}
        mode={isMultiSelect && "multiple"}
        style={styleSelect}
        onChange={this.handleChange}
      >
        {dataSelect.length &&
          dataSelect.length > 0 &&
          dataSelect.map(item => (
            <Select.Option key={item.value} value={item.value}>
              {item.label}
            </Select.Option>
          ))}
      </Select>
    );
  }
}

export default DropDown;
