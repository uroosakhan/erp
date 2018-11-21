function cal_price() {

    var line_total = document.getElementById("line_total").value.replace(/,/g, '');
    var qty = document.getElementById("qty").value.replace(/,/g, '');
    var price_decimal = document.getElementById("price_decimal").value;
    // var price_default = document.getElementById("price_default").value;
    // var qty_default = document.getElementById("qty_default").value;
    // // var price = document.getElementById("price").value.replace(/,/g, '');
    // var primary_price = document.getElementById("primary_price").value.replace(/,/g, '');
   
   
   
    var price = parseFloat(line_total) / parseFloat(qty);
    // var price2 = parseFloat(price_default) * parseFloat(qty_default);
// alert(price_default);
// alert(qty_default);
var n = price.toFixed(price_decimal);
// var o = price2.toFixed(price_decimal);


    if (line_total != 0) {
        document.getElementById("price").value = n;
    }
   else {

        // document.getElementById("line_total").value = price2;
    }
}

function cal_price2() {

    var price = document.getElementById("price").value.replace(/,/g, '');
    var qty = document.getElementById("qty").value.replace(/,/g, '');
    var price_decimal = document.getElementById("price_decimal").value;
    var line_total = parseFloat(price) * parseFloat(qty);
    var n = line_total.toFixed(price_decimal);

        document.getElementById("line_total").value = n;
}