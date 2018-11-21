function calc(){

	var amount =get_amount('amount');
	var wht_supply_amt=get_amount('wht_supply_amt');
	var wht_service_amt=get_amount('wht_service_amt');
	var wht_fbr_amt=get_amount('wht_fbr_amt');
	var wht_srb_amt=get_amount('wht_srb_amt');
	var total = amount - (wht_supply_amt + wht_service_amt +  wht_fbr_amt + wht_srb_amt);
	price_format('amount', total, user.pdec, 0);
	
}
