import React from "react";
import { DragDropContext } from "react-beautiful-dnd";

import Drop from "./Drop";
import "./DragDrop.css";

const getItem = (dropId, dataSource) => {
  const data = [];
  dataSource.forEach(item => {
    if (dropId === "new" && item.cardId === 0) data.push(item);
    else if (dropId === "inProgress" && item.cardId === 1) data.push(item);
    else if (dropId === "completed" && item.cardId === 2) data.push(item);
  });
  return data;
};

const reorder = (list, startIndex, endIndex) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);

  return result;
};

/**
 * Moves an item from one list to another list.
 */
const move = (source, destination, droppableSource, droppableDestination) => {
  const sourceClone = Array.from(source);
  const destClone = Array.from(destination);
  const [removed] = sourceClone.splice(droppableSource.index, 1);

  destClone.splice(droppableDestination.index, 0, removed);

  const result = {};
  result[droppableSource.droppableId] = sourceClone;
  result[droppableDestination.droppableId] = destClone;

  return result;
};

class DragDrop extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      dataNew: getItem("new", props.dataSource),
      dataInProgress: getItem("inProgress", props.dataSource),
      dataCompleted: getItem("completed", props.dataSource)
    };
  }

  listDroppableId = {
    droppableId1: "dataNew",
    droppableId2: "dataInProgress",
    droppableId3: "dataCompleted"
  };

  componentWillReceiveProps(nextProps) {
    if (nextProps.dataSource !== this.props.dataSource)
      this.setState({
        dataNew: getItem("new", nextProps.dataSource),
        dataInProgress: getItem("inProgress", nextProps.dataSource),
        dataCompleted: getItem("completed", nextProps.dataSource)
      });
  }
  getList = id => this.state[this.listDroppableId[id]];

  onDragEnd = result => {
    const { source, destination, draggableId } = result;
    if (!destination) {
      return;
    }
    if (source.droppableId === destination.droppableId) {
      const items = reorder(
        this.getList(source.droppableId),
        source.index,
        destination.index
      );
      if (source.droppableId === "droppableId1") {
        this.setState({ dataNew: items });
      } else if (source.droppableId === "droppableId2") {
        this.setState({ dataInProgress: items });
      } else this.setState({ dataCompleted: items });
    } else {
      const result = move(
        this.getList(source.droppableId),
        this.getList(destination.droppableId),
        source,
        destination
      );
      if (
        source.droppableId === "droppableId1" &&
        destination.droppableId === "droppableId2"
      ) {
        this.setState({
          dataNew: result.droppableId1,
          dataInProgress: result.droppableId2
        });
      } else if (
        source.droppableId === "droppableId1" &&
        destination.droppableId === "droppableId3"
      ) {
        this.setState({
          dataNew: result.droppableId1,
          dataCompleted: result.droppableId3
        });
      } else if (
        source.droppableId === "droppableId2" &&
        destination.droppableId === "droppableId1"
      ) {
        this.setState({
          dataInProgress: result.droppableId2,
          dataNew: result.droppableId1
        });
      } else if (
        source.droppableId === "droppableId2" &&
        destination.droppableId === "droppableId3"
      ) {
        this.setState({
          dataInProgress: result.droppableId2,
          dataCompleted: result.droppableId3
        });
      } else if (
        source.droppableId === "droppableId3" &&
        destination.droppableId === "droppableId1"
      ) {
        this.setState({
          dataCompleted: result.droppableId3,
          dataNew: result.droppableId1
        });
      } else if (
        source.droppableId === "droppableId3" &&
        destination.droppableId === "droppableId2"
      ) {
        this.setState({
          dataCompleted: result.droppableId3,
          dataInProgress: result.droppableId2
        });
      }
      this.props.updateData(draggableId, destination.droppableId);
    }
  };
  render() {
    const { dataNew, dataInProgress, dataCompleted } = this.state;
    const { handleAddTag, handleEditTag, handleDeleteTag } = this.props;
    return (
      <div className="dragdrop-container">
        <DragDropContext onDragEnd={this.onDragEnd}>
          <Drop
            droppableId="droppableId1"
            data={dataNew}
            title="Công việc được phân công"
            handleAddTag={handleAddTag}
            handleEditTag={handleEditTag}
            handleDeleteTag={handleDeleteTag}
          />
          <Drop
            droppableId="droppableId2"
            data={dataInProgress}
            title="Đang làm"
            handleAddTag={handleAddTag}
            handleEditTag={handleEditTag}
            handleDeleteTag={handleDeleteTag}
          />
          <Drop
            droppableId="droppableId3"
            data={dataCompleted}
            title="Hoàn thành"
            handleAddTag={handleAddTag}
            handleEditTag={handleEditTag}
            handleDeleteTag={handleDeleteTag}
          />
        </DragDropContext>
      </div>
    );
  }
}

export default DragDrop;
