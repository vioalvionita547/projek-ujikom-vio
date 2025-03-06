document.getElementById("todo-form").addEventListener("submit", function (event) {
    event.preventDefault();

    let title = document.getElementById("todo-title").value;
    let description = document.getElementById("todo-description").value;
    let deadline = document.getElementById("todo-deadline").value;
    let priority = document.getElementById("todo-priority").value;

    let formData = new FormData();
    formData.append("title", title);
    formData.append("description", description);
    formData.append("deadline", deadline);
    formData.append("priority", priority);

    fetch("add_task.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Task berhasil ditambahkan!");
            location.reload(); // Refresh halaman setelah tambah task
        } else {
            alert("Gagal menambahkan task.");
        }
    })
    .catch(error => console.error("Error:", error));
});