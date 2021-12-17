//declare global variable to store if add or update will be processed
let addUpdate;

//runs when the page is loaded
window.onload = function () {
    //event listeners for buttons - calls method when button is clicked
    document.querySelector("#btnAllData").addEventListener("click", getAllRecords);
    document.querySelector("#btnAdd").addEventListener("click", addCustomer);
    document.querySelector("#btnDelete").addEventListener("click", deleteCustomer);
    document.querySelector("#btnUpdate").addEventListener("click", updateCustomer);
    document.querySelector("#btnDone").addEventListener("click", processForm);
    document.querySelector("#btnCancel").addEventListener("click", hideForm);

    //event handler for each selection on the table - applies to child members - tells which row was clicked
    document.querySelector("table").addEventListener("click", tableClickHandler);

    //calls method to hide the form
    hideForm();

    //enable/disable buttons
    setControls(false, true, true, true);

    //call function to fill the input selections
    getStores();
};

/**
 * function setControls
 * 
 * @param {type} a boolean
 * @param {type} b boolean
 * @param {type} c boolean
 * @param {type} d boolean
 * @returns {undefined} nothing
 * 
 * Enable/disable buttons, considering the parameters sent in
 */
function setControls(a, b, c, d) {
    document.querySelector("#btnAllData").disabled = a;
    document.querySelector("#btnAdd").disabled = b;
    document.querySelector("#btnDelete").disabled = c;
    document.querySelector("#btnUpdate").disabled = d;
}

/**
 * function hideForm
 * 
 * sends nothing
 * returns nothing
 * 
 * Hides the form
 */
function hideForm() {
    document.querySelector("#theForm").classList.add("hidden");
}

/**
 * function showForm
 * 
 * sends nothing
 * returns nothing
 * 
 * makes form visible, determine what to show in the form depending if it's adding or updating
 */
function showForm() {
    //show the form
    document.querySelector("#theForm").classList.remove("hidden");

    //check if adding or updating
    if (addUpdate === "update") {
        //if updating, get the row that is highlighted
        let selectedRow = document.querySelector(".highlight");

        //get array of columns of selected row
        let allColumns = selectedRow.querySelectorAll("td");

        //fill the form with data from selected row + show id field
        document.querySelector("#custID").classList.remove("hidden");
        document.querySelector("#idLabel").classList.remove("hidden");
        document.querySelector("#custID").value = Number(allColumns[0].innerHTML);
        document.querySelector("#firstName").value = allColumns[1].innerHTML;
        document.querySelector("#lastName").value = allColumns[2].innerHTML;
        document.querySelector("#dob").value = allColumns[3].innerHTML;
        document.querySelector("#phone").value = allColumns[4].innerHTML;
        document.querySelector("#email").value = allColumns[5].innerHTML;
        document.querySelector("#loyalty").checked = allColumns[6].innerHTML === "yes" ? true : false;
        document.querySelector("#favStore").value = Number(allColumns[7].innerHTML);

    } else {
        //if adding: clear the form + hide id
        document.querySelector("#custID").classList.add("hidden");
        document.querySelector("#idLabel").classList.add("hidden");
        document.querySelector("#custID").value = 0;
        document.querySelector("#firstName").value = "";
        document.querySelector("#lastName").value = "";
        document.querySelector("#dob").value = "";
        document.querySelector("#phone").value = "";
        document.querySelector("#email").value = "";
        document.querySelector("#loyalty").checked = false;
        document.querySelector("#favStore").value = 0;
    }

}

/**
 * function getAllRecords
 * 
 * sends nothing
 * returns nothing
 * 
 * makes ajax call, get data from php file, calls buildTable  
 */
function getAllRecords() {
    //ajax call to php - gets json data
    let url = "customerService/customers"; // file name or server-side process name
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            let resp = xmlhttp.responseText;
            if (resp.search("ERROR") >= 0) {
                alert("oh no, something is wrong with the GET ...");
            } else {

                //sends json string to buildTable function
                buildTable(xmlhttp.responseText);
            }

            //enable/disable controls
            setControls(true, false, true, true);
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}
/**
 * function deleteCustomer
 * 
 * sends nothing/ returns nothing
 * 
 * gets selected row from table and converts to obj;  makes an ajax call passing in the obj
 */
function deleteCustomer() {
    //get the row that is highlighted
    let selectedRow = document.querySelector(".highlight");

    //get the content from the first cell (= customer id) - first element of array of td 
    let idSelected = Number(selectedRow.querySelector("td").innerHTML);

    if (confirm("Are you sure you want to delete customer " + idSelected + "?")) {
        //create js obj to pass to php
        //let obj = {customerID: idSelected}; // don't need the other fields, only pk

        //ajax call
        let url = "customerService/customers/" + idSelected; //pass the id to delete
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                let resp = xmlhttp.responseText;
                //console.log(resp.search("ERROR"));
                if (resp.search("ERROR") >= 0 || resp != 1) {
                    alert("oh no...");
                   // console.log(resp);
                } else {
                    getAllRecords();
                }
            }
        };
        //don't use GET
        xmlhttp.open("DELETE", url, true); 
        xmlhttp.send();//don't pass the id here
    }
}

/**
 * function addCustomer
 * 
 * sends nothing / returns nothing
 *  
 * changes global variable and shows the form
 */
function addCustomer() {
    //change global variable to "add"
    addUpdate = "add";

    //show the form
    showForm();
}

/**
 * function updateCustomer
 * 
 * sends nothing / returns nothing
 * 
 * changes global variable and shows the form
 */
function updateCustomer() {
    //change global variable
    addUpdate = "update";

    //show the form
    showForm();
}


/**
 * function processForm
 * 
 * sends nothing / returns nothing
 * 
 * makes an ajax call, passing data from the form to the php file 
 */
function processForm() {
    //get data from the form
    let inID = Number(document.querySelector("#custID").value);
    let inFirstName = document.querySelector("#firstName").value;
    let inLastName = document.querySelector("#lastName").value;
    let inDob = document.querySelector("#dob").value;
    let inPhone = document.querySelector("#phone").value;
    let inEmail = document.querySelector("#email").value;
    let inLoyalty = document.querySelector("#loyalty").checked;
    let inFavStore = Number(document.querySelector("#favStore").value);

    //create a js object
    let obj = {
        "customerID": inID,
        "firstName": inFirstName,
        "lastName": inLastName,
        "dob": inDob,
        "phone": inPhone,
        "email": inEmail,
        "loyalty": inLoyalty,
        "favStore": inFavStore
    };

    //Make AJAX call to add or update the record in the database.
    let url ="customerService/customers/" + inID;
    let method = (addUpdate === "add") ? "POST" : "PUT";
    //console.log(url);
    let xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            //returns the text received from a server following a request being sent 
            //resp contains either the textual data received using the XMLHttpRequest 
            //or null if the request failed 
            //or "" if the request has not yet been sent by calling send()
            let resp = xmlhttp.responseText;
            //console.log(resp);
            if (resp.search("ERROR") >= 0 || resp != 1) {
                alert("oh no...");

            } else {
                getAllRecords();
            }
            //call functions
            hideForm();
        }
    };
    
    xmlhttp.open(method, url, true);
    xmlhttp.send(JSON.stringify(obj)); //pass the entire obj with the request 
   }
    
    
/**
 * function getStores
 * 
 * sends nothing
 * returns nothing
 * 
 * makes ajax call, get data from php file, calls buildSelection  
 */
function getStores() {
    //ajax call to php - gets json data
    let url = "storeService/stores"; // file name or server-side process name
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            let resp = xmlhttp.responseText;//this is a json string
            if (resp.search("ERROR") >= 0) {
                alert("oh no...");
            } else {
                //sends json string to buildSelect function
                buildSelect(resp);
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

/**
 * function buildSelect
 * 
 * @param {type} jsonStr = string in json format
 * @returns {undefined}
 */
function buildSelect(jsonStr) {
    //convert json string into array of js objects
    let storesArr = JSON.parse(jsonStr);

    //declare variable to store the html string
    let html = "";

    //loop through array
    for (let i = 0; i < storesArr.length; i++) {
        html += "<option value='" + storesArr[i].code + "'>" + storesArr[i].code + "</option><br>";
    }

    //add to html file
    document.querySelector("#favStore").innerHTML = html;
}

/**
 * function buildTable
 * 
 * @param {type} jsonStr = string in json format
 * @returns {undefined}
 */
function buildTable(jsonStr) {
    //convert json string into array of js objects
    let customersArr = JSON.parse(jsonStr);

    //declare variable to store the html string - start table header
    let htmlStr = "<tr>" +
            "<th>Customer ID</th>" +
            "<th>First Name</th>" +
            "<th>Last Name</th>" +
            "<th>Date of Birth</th>" +
            "<th>Phone</th>" +
            "<th>Email</th>" +
            "<th>Is loyalty member?</th>" +
            "<th>Favorite Store</th>" +
            "</tr>";

    //  loop through customers array adding data to next rows
    for (let i = 0; i < customersArr.length; i++) {
        htmlStr += "<tr>" +
                "<td>" + customersArr[i].customerID + "</td>" +
                "<td>" + customersArr[i].firstName + "</td>" +
                "<td>" + customersArr[i].lastName + "</td>" +
                "<td>" + customersArr[i].dob + "</td>" +
                "<td>" + customersArr[i].phone + "</td>" +
                "<td>" + customersArr[i].email + "</td>" +
                "<td>" + (customersArr[i].loyalty === 1 ? "yes" : "no") + "</td>" +
                "<td>" + customersArr[i].favStore + "</td>" +
                "</tr>";
    }

    //add html string to html file
    document.querySelector("table").innerHTML = htmlStr;
}






/**
 * function tableClickHandler
 * 
 * @param {type} row clicked
 * @returns {undefined}
 * 
 * clears previous selections, highlights the row clicked, enable/disable buttons, and hides the form
 */
function tableClickHandler(row) {
    //clear all previous selections
    clearAllSelections();

    //add highlight class to selected row
    row.target.parentElement.classList.add("highlight");

    //enable/disable buttons
    setControls(true, false, false, false);

    //hideform
    hideForm();
}

/**
 * function clearAllSelections
 * 
 * sends nothing/ returns nothing
 * 
 * clears all previous highlights on the table
 */
function clearAllSelections() {
    //loop through table removing highlight class
    let allRows = document.querySelectorAll("tr");
    for (let i = 0; i < allRows.length; i++) {
        allRows[i].classList.remove("highlight");
    }
}