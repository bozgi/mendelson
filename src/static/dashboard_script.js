const createGraphButton = document.querySelector('.create-graph-button');
const removeGraphButtons = document.querySelectorAll('.remove-graph-button');

createGraphButton.addEventListener('click', () => {
    editDialog.showModal();
});

cancel.addEventListener('click', () => {
    editDialog.close();
});

cancelGraph.addEventListener('click', () => {
    editGraphDialog.close();
});

temperature.addEventListener('input', () => {
    let value = temperature.value;

    value = value.replace(/[^0-9.]/g, '');

    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }

    temperature.value = value;
});

submitGraph.addEventListener('click', () => {
    fetch("api/update_graph.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            id: document.getElementById('graphId').value,
            date: document.getElementById('date').value,
            temperature: document.getElementById('temperature').value,
            status: document.getElementById('status').value,
            graphId: document.getElementById('graphId').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            editGraphDialog.close();
            fetchGraph(document.getElementById('graphId').value).then(graphData => {
                if (graphData) {
                    const graphImg = document.querySelector('.graphDialog img');
                    graphImg.src = graphData.image;
                    const map = document.querySelector('.graphDialog map[name="graph-map"]');
                    map.innerHTML = '';
                    graphData.points.forEach(point => {
                        const area = document.createElement('area');
                        area.shape = 'circle';
                        area.coords = `${point.x},${point.y},5`;
                        area.dataset.date = point.date;
                        area.dataset.temperature = point.temperature_c;
                        area.dataset.status = point.status;
                        area.dataset.graphId = document.getElementById('graphId').value;
                        map.appendChild(area);
                    });
                    attachAreaListeners();
                }
            });
        }
    })
    .catch(error => console.error('Error updating graph:', error));
});

ok.addEventListener('click', () => {
    const startDateInput = document.querySelector('.start-date');
    const endDateInput = document.querySelector('.end-date');
    const dialogInfo = document.querySelector('.dialog-info');

    if (startDateInput.value === "" || endDateInput.value === "") {
        dialogInfo.innerText = "Proszę wypełnić oba pola daty.";
        // editDialog.showModal();
        return;
    }

    if (new Date(startDateInput.value) > new Date(endDateInput.value)) {
        dialogInfo.innerText = "Data początkowa nie może być późniejsza niż data końcowa.";
        // editDialog.showModal();
        return;
    }

    fetch("api/create_graph.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            startDate: startDateInput.value,
            endDate: endDateInput.value
        })
    })
    .then(response => response.json())
    .then(() => location.reload())

});

async function fetchGraph(id) {
    const response = await fetch(`api/get_graph.php?id=${id}&width=1000&height=750`);
    const data = await response.json();
    if (data.success) {
        console.log(data);
        return {
            image: `data:image/png;base64,${data.image}`,
            points: data.points
        };
    } else {
        console.error('Error fetching graph: ' + data.message);
        return null;
    }
}

function attachAreaListeners() {
    const areas = document.querySelectorAll('area');
    const dialog = document.querySelector('#editGraphDialog');
    areas.forEach(area => {
        area.addEventListener('click', () => {
            const date = area.dataset.date;
            const temperature = area.dataset.temperature;
            const status = area.dataset.status;
            const graphId = area.dataset.graphId;

            document.getElementById('date').value = date;
            document.getElementById('dateDisplay').textContent = date;
            document.getElementById('temperature').value = temperature !== 'n/a' ? temperature : '';
            document.getElementById('status').value = status;
            document.getElementById('graphId').value = graphId;

            dialog.showModal();
        });
    });
}

function loadGraphs() {
    const container = document.querySelector('.graphs-container');

    function attachRemoveButtonListeners() {
        const removeGraphButtons = document.querySelectorAll('.remove-graph-button');
        removeGraphButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const graphId = button.dataset.id;
                fetch(`api/delete_graph.php`, {
                    method: 'POST',
                    body: JSON.stringify({  graphId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadGraphs();
                    } else {
                        console.error('Error deleting graph: ' + data.message);
                    }
                })
                .catch(error => console.error('Error deleting graph:', error));
            });
        });
    }

    function attachGraphListeners() {
        const graphItems = document.querySelectorAll('.graph-item');
        graphItems.forEach(item => {
            item.addEventListener('click', () => {
                const graphDialog = document.querySelector(".graphDialog");
                const graphId = item.dataset.id;
                const graphImg = graphDialog.querySelector('img');

                fetchGraph(graphId).then(graphData => {
                    if (graphData) {
                        graphImg.src = graphData.image;
                        const map = graphDialog.querySelector('map[name="graph-map"]');
                        map.innerHTML = '';
                        graphData.points.forEach(point => {
                            const area = document.createElement('area');
                            area.shape = 'circle';
                            area.coords = `${point.x},${point.y},5`;
                            area.dataset.date = point.date;
                            area.dataset.temperature = point.temperature_c;
                            area.dataset.status = point.status;
                            area.dataset.graphId = graphId;
                            map.appendChild(area);
                        });
                        attachAreaListeners();
                        graphDialog.showModal();
                    }
                });
            });
        });
    }

    fetch('api/get_graphs.php')
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';
            data.forEach(graph => {
                const graphElement = document.createElement('div');
                graphElement.classList.add('graph-item');
                graphElement.innerHTML = `
                    <p>${graph.start_date}</p>
                    <p>${graph.end_date}</p>
                    <div class="graph-item-buttons">
                        <a href="api/pdf.php?id=${graph.id}" target="_blank"><i class="fa-solid fa-file-pdf fa-lg"></i></a>
                        <button class="remove-graph-button" data-id="${graph.id}"><i class="fa-solid fa-trash fa-lg"></i></button>
                    </div>
                `;
                graphElement.dataset.id = graph.id;
                container.appendChild(graphElement);
                if (graph.id == OPEN_GRAPH_ID) {
                    console.log("bingo", graphElement);
                    OPEN_GRAPH_BUTTON = graphElement;
                }
            });
            attachGraphListeners();
            attachRemoveButtonListeners();
            if (OPEN_GRAPH_BUTTON) {
                OPEN_GRAPH_BUTTON.click();
            }
        })
        .catch(error => {
            console.error('Error loading graphs:', error);
        });
}

document.addEventListener('DOMContentLoaded', () => {
    loadGraphs();
});