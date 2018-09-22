import React from "react";
import { Droppable } from "react-beautiful-dnd";
import { Icon } from "antd";

import Drag from "./Drag";

const getListStyle = isDraggingOver => ({
  background: isDraggingOver ? "lightblue" : "#E0F7FA",
  padding: 20,
  width: "33%"
});

class Drop extends React.Component {
  handleAddTag = () => {
    this.props.handleAddTag(this.props.droppableId);
  };
  render() {
    const {
      data,
      droppableId,
      title,
      handleEditTag,
      handleDeleteTag
    } = this.props;
    return (
      <Droppable droppableId={droppableId}>
        {(provided, snapshot) => (
          <div
            ref={provided.innerRef}
            type="BOARD"
            style={getListStyle(snapshot.isDraggingOver)}
          >
            <div className="title-drop">
              <span>{title}</span>
              &nbsp;
              <span>{`(${this.props.data.length})`}</span>
            </div>
            {/* <div className="add-tag" onClick={this.handleAddTag}>
              <span className="text-content"><Icon type="plus" theme="outlined" />&nbsp;&nbsp;New Task</span>
            </div> */}
            {data.map((item, index) => (
              <Drag
                key={item.id + index}
                item={item}
                index={index}
                handleEditTag={handleEditTag}
                handleDeleteTag={handleDeleteTag}
              />
            ))}
            {provided.placeholder}
          </div>
        )}
      </Droppable>
    );
  }
}

export default Drop;
