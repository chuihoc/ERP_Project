import React from "react";
import { Draggable } from "react-beautiful-dnd";
import { Popconfirm } from "antd";

const grid = 8;

const getItemStyle = (isDragging, draggableStyle) => ({
  // some basic styles to make the items look a bit nicer
  userSelect: "none",
  padding: grid * 2,
  margin: `0 0 ${grid}px 0`,
  // change background colour if dragging
  background: isDragging ? "lightgreen" : "white",
  boxShadow: "1px 2px #E0E0E0",
  // styles we need to apply on draggables
  ...draggableStyle
});

class Drag extends React.Component {
  handleEditTag = () => {
    this.props.handleEditTag(this.props.item);
  };

  handleDeleteTag = () => {
    this.props.handleDeleteTag(this.props.item);
  };
  render() {
    const { item, index } = this.props;
    return (
      <Draggable key={item.id} draggableId={item.id} index={index}>
        {(provided, snapshot) => (
          <div
            ref={provided.innerRef}
            {...provided.draggableProps}
            {...provided.dragHandleProps}
            style={getItemStyle(
              snapshot.isDragging,
              provided.draggableProps.style
            )}
          >
            <span style={{ fontSize: 16 }}> {item.text}</span>
            <br />
            <span
              style={{
                marginBottom: 0,
                fontSize: 12,
                color: item.cardId === 1 && "red"
              }}
            >
              {item.start_date}
            </span>
            <span className="action-tag">
              <span className="func-edit-delete" onClick={this.handleEditTag}>
                Detail
              </span>
              <span>|</span>
              <Popconfirm
                title="Are you sure delete this task?"
                onConfirm={this.handleDeleteTag}
                okText="Yes"
                cancelText="No"
              >
                <span className="func-edit-delete">Delete</span>
              </Popconfirm>
            </span>
          </div>
        )}
      </Draggable>
    );
  }
}

export default Drag;
