<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1>Create User</h1>
        <form id="dataForm">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group col-md-4"">
                <label for="age">Age:</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>

        <h2 class="mt-5">User Data</h2>
        <table id="dataTable" class="table">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    <script>
        const request = indexedDB.open("FormDataDB", 1);

        request.onupgradeneeded = function(event) {
            const db = event.target.result;
            const objectStore = db.createObjectStore("formData", {
                keyPath: "id",
                autoIncrement: true
            });
            objectStore.createIndex("name", "name", {
                unique: false
            });
            objectStore.createIndex("email", "email", {
                unique: true
            });
            objectStore.createIndex("age", "age", {
                unique: false
            });
        };

        request.onsuccess = function(event) {
            const db = event.target.result;
            const form = document.getElementById("dataForm");
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const name = document.getElementById("name").value;
                const email = document.getElementById("email").value;
                const age = document.getElementById("age").value;

                const transaction = db.transaction(["formData"], "readwrite");
                const objectStore = transaction.objectStore("formData");

                const data = {
                    name,
                    email,
                    age
                };
                const request = objectStore.add(data);

                request.onsuccess = function() {
                    console.log("Data added to the store", request.result);
                    displayData();
                };

                request.onerror = function() {
                    console.error("Error adding data", request.error);
                };
            });

            displayData();
        };

        request.onerror = function(event) {
            console.error("Database error: ", event.target.errorCode);
        };

        // Function to display data in the table
        function displayData() {
            const db = request.result;
            const transaction = db.transaction(["formData"], "readonly");
            const objectStore = transaction.objectStore("formData");

            const tableBody = document.querySelector("#dataTable tbody");
            tableBody.innerHTML = "";

            objectStore.openCursor().onsuccess = function(event) {
                const cursor = event.target.result;
                if (cursor) {
                    const row = document.createElement("tr");
                    row.innerHTML =
                        `<td>${cursor.value.name}</td><td>${cursor.value.email}</td><td>${cursor.value.age}</td>`;
                    tableBody.appendChild(row);
                    cursor.continue();
                }
            };
        }
    </script>
</body>

</html>