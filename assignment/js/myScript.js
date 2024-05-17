var transaction = new Array(); //used in the sales screen
var ProductSearch = new Array(); //used in the pick a product screen

var summary = 0;
var selected_index;
var product_index = -1;

/***************************************************************************
						START OF POS SALES SCREEN FUNCTIONS
****************************************************************************/
var x = "";
function init() {
  var mytable =
    "<table><thead><tr><th>Code</th><th>Product Name</th><th>Unit Price</th><th>Qty</th><th>Total</th></tr></thead></table>";
  document.getElementById("table").innerHTML = mytable;
  setCurrent("POS");
  keyBoardSetup();
}
function add_item(pcode, q, pna, pp) {
  if (pna == "Product Not Found!") {
    alert("Product not found!");
    return;
  }
  if (pcode == "") {
    alert("Product not found!");
    return;
  }
  if (typeof q === "undefined") {
    q = 1;
  }
  if (typeof q === "string" && q == "") {
    q = 1;
  }

  document.getElementById("table").innerHTML = "";
  var theID = "";
  //var mytable = "<table id='DataTable'><thead><tr><th>Code</th><th>Product Name</th><th>Unit</th><th>Qty</th><th>Total</th></tr></thead>";
  summary += pp * q;
  transaction.unshift({
    ProdID: pcode,
    ProdName: pna,
    AmtSold: q,
    UnitPrice: pp,
  });
  selected_index = 0;
  refreshGrid();
  //$("#table").animate({ scrollTop: 0 }, "fast");
  document.getElementById("summary").style.display = "block";
  document.getElementById("summary").innerHTML = "$" + summary.toFixed(2);
  document.getElementById("xpcode").value = "";
  document.getElementById("pname_nf").innerText = "";
  document.getElementById("xpcode").focus();
  document.getElementById("save").style.display = "block";
  document.getElementById("cancel").style.display = "block";
  //document.getElementById("inst").style.display = "block";
}
function saveit() {
  let paymentType = document.getElementById("type").value;
  let tenderAmount = document.getElementById("tenderedamount").value;

  console.log(paymentType);
  console.log(tenderAmount);
  let formData = new FormData();
  formData.append("salesno", "");
  formData.append("salesdate", "");
  formData.append("salestotal", summary.toFixed(2));
  formData.append("items", JSON.stringify(transaction));
  formData.append("type", paymentType);
  formData.append("tenderedamount", tenderAmount);

  fetch("php/addSale.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.text())
    .then((data) => {
      window.location.reload(true);
    });
}
function cancelit() {
  window.location.reload(true);
}
//$(document).on("click", "body", function(e){
//document.getElementById('xpcode').focus();
//});
function keyBoardSetup() {
  x = sessionStorage.getItem("currentPage");
  if (x == "POS") {
    document.getElementById("xpcode").addEventListener("keydown", (e) => {
      if (e.key == "Enter") {
        var code = document.getElementById("xpcode").value;
        if (code) {
          let formData = new FormData();
          formData.append("code", code);
          fetch("php/getitem.php", {
            method: "post",
            body: formData,
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.Name == "") {
                document.getElementById("pname_nf").innerHTML =
                  "Product Not Found!";
              } else {
                document.getElementById("pname_nf").innerHTML = "";
                add_item(code, 1, data.Name, data.Unit);
              }
            });
        }
      }
      if (e.key == "ArrowUp") {
        selected_index--;
        if (selected_index < 0) {
          selected_index = 0;
        }
        if (transaction.length == 0) {
          return;
        }
        refreshGrid();
        var elmnt = document.getElementById("table");
        elmnt.scrollTop -= 40;
      }
      if (e.key == "ArrowDown") {
        selected_index++;
        if (transaction.length == 0) {
          return;
        }
        var transLen = transaction.length;
        if (selected_index > transLen - 1) {
          selected_index = transLen - 1;
        }
        refreshGrid();
        if (selected_index > 3 && selected_index <= transLen - 1) {
          var elmnt = document.getElementById("table");
          elmnt.scrollTop += 40;
        }
      }
      if (e.key == "ArrowRight") {
        transaction[selected_index]["AmtSold"]++;
        refreshGrid();
      }
      if (e.key == "ArrowLeft") {
        transaction[selected_index]["AmtSold"]--;
        refreshGrid();
      }
      if (e.key == "Delete") {
        transaction.splice(selected_index, 1);
        refreshGrid();
      }
    });
    document.getElementById("xpcode").addEventListener("input", (e) => {
      var code = document.getElementById("xpcode").value;
      if (isNaN(code)) {
        if (code.length >= 2) {
          modal.style.display = "block";
          document.getElementById("psearch").value = code;
          document.getElementById("psearch").focus();
          on_first_display(code);
        }
      }
    });
  }
  if (x == "PROD") {
    document.getElementById("pcode").addEventListener("keydown", (e) => {
      if (e.key == "Enter") {
        search(document.getElementById("pcode").value);
      }
    });
    document.getElementById("pcode").addEventListener("input", (e) => {
      let code = document.getElementById("pcode").value;
      if (isNaN(code)) {
        if (code.length >= 2) {
          modal.style.display = "block";
          document.getElementById("psearch").value = code;
          document.getElementById("psearch").focus();
          on_first_display(code);
        }
      }
    });
  }
}
function refreshGrid() {
  summary = 0;
  var theID = "";
  var mytable =
    "<table><tr><th>Code</th><th>Product Name</th><th>Unit Price</th><th>Qty</th><th>Total</th></tr>";
  transaction.forEach(myFunction);
  function myFunction(item, index) {
    theID = "";
    summary += item.UnitPrice * item.AmtSold;
    if (index == selected_index) {
      theID = "class = 'selected'";
    }
    mytable +=
      "<tr " +
      theID +
      "> <td>" +
      item.ProdID +
      "</td><td class=pro_grid_name>" +
      item.ProdName +
      "</td><td>" +
      item.UnitPrice +
      "</td><td>" +
      item.AmtSold +
      "</td><td>" +
      (item.AmtSold * item.UnitPrice).toFixed(2) +
      "</td></tr>";
  }
  mytable += "</table>";
  document.getElementById("table").innerHTML = mytable;
  document.getElementById("summary").innerHTML = "$" + summary.toFixed(2);
}

/***************************************************************************
					 END OF SALES SCREEN FUNCTIONS
****************************************************************************/

/***************************************************************************
					 START OF PICK-A-PRODUCT SCREEN FUNCTIONS
****************************************************************************/

document.getElementById("psearch").addEventListener("input", (e) => {
  var code = e.target.value;
  ProductSearch.length = 0;
  let formData = new FormData();
  formData.append("code", code);
  fetch("php/getProdName.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.length == 0) {
        ProductSearch.length = 0;
        refreshProductGrid();
        return;
      }
      data.forEach((item) => {
        ProductSearch.push({
          ProdID: item.code,
          ProdName: item.name,
          ProdPrice: item.price,
        });
      });
      product_index = 0;
      refreshProductGrid();
    });
});
document.getElementById("psearch").addEventListener("keydown", (e) => {
  if (e.key == "Enter") {
    if (ProductSearch.length <= 0) {
      return;
    }
    let x = sessionStorage.getItem("currentPage");
    if (x == "POS") {
      add_item(
        ProductSearch[product_index]["ProdID"],
        1,
        ProductSearch[product_index]["ProdName"],
        ProductSearch[product_index]["ProdPrice"]
      );
      document.getElementById("psearch").value = "";
      document.getElementById("pro_table").innerHTML = "";
      modal.style.display = "none";
      document.getElementById("xpcode").focus();
    }
    if (x == "PROD") {
      search(ProductSearch[product_index]["ProdID"]);
      document.getElementById("psearch").value = "";
      document.getElementById("pro_table").innerHTML = "";
      modal.style.display = "none";
    }
  }
  if (e.key == "ArrowUp") {
    product_index--;
    if (product_index < 0) {
      product_index = 0;
    }
    if (ProductSearch.length == 0) {
      return;
    }
    refreshProductGrid();
    let elmnt = document.getElementById("pro_table");
    elmnt.scrollTop -= 40;
  }
  if (e.key == "ArrowDown") {
    product_index++;
    if (ProductSearch.length == 0) {
      return;
    }
    var transLen = ProductSearch.length;
    if (product_index > transLen - 1) {
      product_index = transLen - 1;
    }
    refreshProductGrid();
    let elmnt = document.getElementById("pro_table");
    elmnt.scrollTop += 40;
  }
});
function on_first_display(code = "") {
  ProductSearch.length = 0;
  let formData = new FormData();
  formData.append("code", code);
  fetch("php/getProdName.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.length == 0) {
        ProductSearch.length = 0;
        refreshProductGrid();
        return;
      }
      data.forEach((item) => {
        ProductSearch.push({
          ProdID: item.code,
          ProdName: item.name,
          ProdPrice: item.price,
        });
      });
      product_index = 0;
      refreshProductGrid();
    });
}
function refreshProductGrid() {
  var theID = "";
  var mytable =
    "<table id='pick'><tr><th>Product Name</th><th style='width : 100px;'>Unit Price</th></tr>";
  ProductSearch.forEach(myFunction);
  function myFunction(item, index) {
    theID = "";
    if (index == product_index) {
      theID = "class = 'selected'";
    }
    mytable +=
      "<tr " +
      theID +
      " onclick='rowClicked(" +
      index +
      ")'> <td>" +
      item.ProdName +
      "</td><td>" +
      item.ProdPrice +
      "</td></tr>";
  }
  mytable += "</table>";
  //document.getElementById("inst_search").style.display = "block";
  document.getElementById("pro_table").innerHTML = mytable;
}
function rowClicked(index) {
  product_index = index;
  var x = sessionStorage.getItem("currentPage");
  if (x == "POS") {
    add_item(
      ProductSearch[product_index]["ProdID"],
      1,
      ProductSearch[product_index]["ProdName"],
      ProductSearch[product_index]["ProdPrice"]
    );
    document.getElementById("psearch").value = "";
    document.getElementById("pro_table").innerHTML = "";
    //document.getElementById("inst_search").style.display = "none";
    modal.style.display = "none";
    document.getElementById("xpcode").focus();
  }
  if (x == "PROD") {
    search(ProductSearch[product_index]["ProdID"]);
    document.getElementById("psearch").value = "";
    //document.getElementById("inst_search").style.display = "none";
    document.getElementById("pro_table").innerHTML = "";
    modal.style.display = "none";
  }
}

function product_search() {
  modal.style.display = "block";
  document.getElementById("psearch").focus();
  on_first_display();
}

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementById("close");

if (span != null) {
  // When the user clicks on <span> (x), close the modal
  span.onclick = function () {
    document.getElementById("psearch").value = "";
    document.getElementById("pro_table").innerHTML = "";
    //document.getElementById("inst_search").style.display = "none";
    modal.style.display = "none";
    let x = sessionStorage.getItem("currentPage");
    if (x == "POS") {
      document.getElementById("xpcode").focus();
    }
    if (x == "PROD") {
      document.getElementById("pcode").focus();
    }
  };
}

// When the user clicks anywhere outside of the modal, close it
//window.onclick = function(event) {
//if (event.target == modal) {
//modal.style.display = "none";
//}
//}
/***************************************************************************
					 END OF PICK-A-PRODUCT SCREEN FUNCTIONS
****************************************************************************/

/***************************************************************************
					 START OF PRODUCT SCREEN FUNCTIONS
****************************************************************************/

function pro_last() {
  fetch("php/getLast.php")
    .then((res) => res.json())
    .then((data) => {
      updateTextBoxes(data);
    });
}
function pro_first() {
  fetch("php/getFirst.php")
    .then((res) => res.json())
    .then((data) => {
      updateTextBoxes(data);
    });
}
function pro_prev() {
  var x = sessionStorage.getItem("_id");
  if (x == 0) {
    return;
  }
  if (x == sessionStorage.getItem("first_ID")) {
    alert("This is the first document in the collection.");
    return;
  }
  let formData = new FormData();
  formData.append("_id", x);
  fetch("php/getPrev.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      updateTextBoxes(data);
    });
}
function pro_next() {
  var x = sessionStorage.getItem("_id");
  if (x == 0) {
    return;
  }
  if (x == sessionStorage.getItem("last_ID")) {
    alert("This is the last document in the collection.");
    return;
  }
  let formData = new FormData();
  formData.append("_id", x);
  fetch("php/getNext.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      updateTextBoxes(data);
    });
}
function pro_saveit() {
  var pcode = document.getElementById("pcode").value;
  var pname = document.getElementById("pname").value;
  var pcost = document.getElementById("pcost").value;
  var pprice = document.getElementById("pprice").value;
  var ponhand = document.getElementById("onhand").value;
  var pvendors = document.getElementById("vendors").value;
  if (pcode == "" || pname == "") {
    alert("Please enter all the data");
    document.getElementById("pcode").focus();
    return;
  }
  pvendors = pvendors.replace(/ ,/g, ",");
  pvendors = pvendors.replace(/ , /g, ",");
  pvendors = pvendors.replace(/, /g, ",");
  pvendors = pvendors.split(",");
  if (document.getElementById("psave").value == "Save") {
    let formData = new FormData();
    formData.append("code", pcode);
    formData.append("name", pname);
    formData.append("cost", pcost);
    formData.append("price", pprice);
    formData.append("onhand", ponhand);
    formData.append("vendors", JSON.stringify(pvendors)); //has to stringify the array before it is sent of to the server
    fetch("php/addProduct.php", {
      method: "post",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        //alert(data)
        pro_cancelit();
      });
  } else {
    var x = sessionStorage.getItem("_id");
    let formData = new FormData();
    formData.append("_id", x);
    formData.append("code", pcode);
    formData.append("name", pname);
    formData.append("cost", pcost);
    formData.append("price", pprice);
    formData.append("onhand", ponhand);
    formData.append("vendors", JSON.stringify(pvendors));
    fetch("php/updateProduct.php", {
      method: "post",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        //alert(data)
        pro_cancelit();
      });
  }
}
function search(code) {
  let formData = new FormData();
  formData.append("code", code);
  fetch("php/getProduct.php", {
    method: "post",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.Name != "") {
        updateTextBoxes(data);
        document.getElementById("psave").value = "Update";
      }
    });
}
function pro_cancelit() {
  document.getElementById("pcode").value = "";
  document.getElementById("pname").value = "";
  document.getElementById("pcost").value = "";
  document.getElementById("pprice").value = "";
  document.getElementById("onhand").value = "";
  document.getElementById("vendors").value = "";
  document.getElementById("psave").value = "Save";
  document.getElementById("pcode").focus();
  sessionStorage.setItem("_id", 0);
}
function pro_deleteit() {
  var x = sessionStorage.getItem("_id");
  if (x == 0) {
    alert("No document to delete.");
    return;
  }
  if (confirm("Are you sure you want to delete this document?") == true) {
    let formData = new FormData();
    formData.append("_id", x);
    fetch("php/deleteProduct.php", {
      method: "post",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        first_last();
        pro_cancelit();
      });
  }
}
function updateTextBoxes(data) {
  sessionStorage.setItem("_id", data["_id"]);
  document.getElementById("pcode").value = data["Code"];
  document.getElementById("pname").value = data["Name"];
  document.getElementById("pcost").value = data["Cost"];
  document.getElementById("pprice").value = data["Price"];
  document.getElementById("onhand").value = data["Onhand"];
  document.getElementById("vendors").value = data["vendors"];
  document.getElementById("psave").value = "Update";
}
function first_last() {
  fetch("php/first_last.php")
    .then((res) => res.json())
    .then((data) => {
      sessionStorage.setItem("first_ID", data["first_ID"]);
      sessionStorage.setItem("last_ID", data["last_ID"]);
    });
  setCurrent("PROD");
  keyBoardSetup();
}
x = sessionStorage.getItem("currentPage");
if (x == "PROD") {
  document.getElementById("pcode").addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
      search(document.getElementById("pcode").value);
    }
  });
}

/***************************************************************************
					 END OF PRODUCT SCREEN FUNCTIONS
****************************************************************************/

/***************************************************************************
					 START OF MISCELLANEOUS FUNCTIONS
****************************************************************************/

function setCurrent(page) {
  sessionStorage.setItem("currentPage", page);
}
/*
function tableText() {
  alert(this.innerHTML);
}

var cells = document.querySelectorAll("td")

for (var i = 0; i < cells.length; i++){
  cells[i].addEventListener("click", tableText)
}
*/
