<?php

class complete_invoice_app extends application
{
	function __construct()
	{
		//$this->application("CI", _($this->help_context = "&Complete Invoice"));
        parent::__construct("CI", _($this->help_context = "&Complete Invoice"));
		$this->add_module(_("Inquiry"));
		$this->add_lapp_function(0, _(get_company_pref_display('complete_voucher_text')),
			"complete_voucher/inquiry/complete_voucher_inquiry.php?", 'SA_COMPLETEINVOICE', MENU_INQUIRY);

//			$this->add_lapp_function(0, _(get_company_pref_display('bank_payments_voucher_text')),
//				"gl/gl_bank.php?NewPayment=Yes", 'SA_PAYMENT', MENU_TRANSACTION);
//
//		if (get_company_pref_display('bank_deposit_voucher'))
//			$this->add_lapp_function(0, _(get_company_pref_display('bank_deposit_voucher_text')),
//				"gl/gl_bank.php?NewDeposit=Yes", 'SA_DEPOSIT', MENU_TRANSACTION);
//
//		if (get_company_pref_display('cash_payments_voucher'))
//			$this->add_lapp_function(0, _(get_company_pref_display('cash_payments_voucher_text')),
//				"gl/gl_bankCV.php?NewPayment=Yes", 'SA_PAYMENT', MENU_TRANSACTION);
//
//		if (get_company_pref_display('cash_receipt_voucher'))
//			$this->add_lapp_function(0, _(get_company_pref_display('cash_receipt_voucher_text')),
//				"gl/gl_bankCV.php?NewDeposit=Yes", 'SA_DEPOSIT', MENU_TRANSACTION);
//
//		if (get_company_pref_display('bank_account_transfer'))
//			$this->add_lapp_function(0, _(get_company_pref_display('bank_account_transfer_text')),
//				"gl/bank_transfer.php?", 'SA_BANKTRANSFER', MENU_TRANSACTION);
//
//		if (get_company_pref_display('journal_inquiry'))
//			$this->add_rapp_function(0, _(get_company_pref_display('journal_entry_text')),
//				"gl/gl_journal.php?NewJournal=Yes", 'SA_COMPLETEINVOICE', MENU_TRANSACTION);
//
//
//		if (get_company_pref_display('budget_entry'))
//			$this->add_rapp_function(0, _(get_company_pref_display('budget_entry_text')),
//				"gl/gl_budget.php?", 'SA_BUDGETENTRY', MENU_TRANSACTION);
//
//
//		if (get_company_pref_display('reconcile_account'))
//			$this->add_rapp_function(0, _(get_company_pref_display('reconcile_account_text')),
//				"gl/bank_account_reconcile.php?", 'SA_RECONCILE', MENU_TRANSACTION);
//
//		if (get_company_pref_display('revenue_cost'))
//			$this->add_rapp_function(0, _(get_company_pref_display('revenue_cost_text')),
//				"gl/accruals.php?", 'SA_ACCRUALS', MENU_TRANSACTION);
//
//		$this->add_module(_("Inquiries and Reports"));
//
//		if (get_company_pref_display('journal_inquiry'))
//
//
//		if (get_company_pref_display('gl'))
//			$this->add_lapp_function(1, _(get_company_pref_display('gl_text')),
//				"gl/inquiry/gl_account_inquiry.php?", 'SA_GLTRANSVIEW', MENU_INQUIRY);
//
//		if (get_company_pref_display('bank_account_inquiry'))
//			$this->add_lapp_function(1, _(get_company_pref_display('bank_account_inquiry_text')),
//				"gl/inquiry/bank_inquiry.php?", 'SA_BANKTRANSVIEW', MENU_INQUIRY);
//
//		if (get_company_pref_display('tax_inquiry'))
//			$this->add_lapp_function(1, _(get_company_pref_display('tax_inquiry_text')),
//				"gl/inquiry/tax_inquiry.php?", 'SA_TAXREP', MENU_INQUIRY);
//
//		if (get_company_pref_display('trial_balance'))
//			$this->add_rapp_function(1, _(get_company_pref_display('trial_balance_text')),
//				"gl/inquiry/gl_trial_balance.php?", 'SA_GLANALYTIC', MENU_INQUIRY);
//
//
//		if (get_company_pref_display('drill_balance'))
//			$this->add_rapp_function(1, _(get_company_pref_display('drill_balance_text')),
//				"gl/inquiry/balance_sheet.php?", 'SA_GLANALYTIC', MENU_INQUIRY);
//
//
//		if (get_company_pref_display('pf_drill'))
//			$this->add_rapp_function(1, _(get_company_pref_display('pf_drill_text')),
//				"gl/inquiry/profit_loss.php?", 'SA_GLANALYTIC', MENU_INQUIRY);
//
//
//		if (get_company_pref_display('banking_reports'))
//			$this->add_rapp_function(1, _(get_company_pref_display('banking_reports_text')),
//				"reporting/reports_main.php?Class=5", 'SA_BANKREP', MENU_REPORT);
//
//
//		if (get_company_pref_display('general_ledger'))
//			$this->add_rapp_function(1, _(get_company_pref_display('general_ledger_text')),
//				"reporting/reports_main.php?Class=6", 'SA_GLREP', MENU_REPORT);
//
//		$this->add_module(_("Maintenance"));
//
//		if (get_company_pref_display('bank_account'))
//			$this->add_lapp_function(2, _(get_company_pref_display('bank_account_text')),
//				"gl/manage/bank_accounts.php?", 'SA_BANKACCOUNT', MENU_MAINTENANCE);
//
//		if (get_company_pref_display('quick_entries'))
//			$this->add_lapp_function(2, _(get_company_pref_display('quick_entries_text')),
//				"gl/manage/gl_quick_entries.php?", 'SA_QUICKENTRY', MENU_MAINTENANCE);
//
//
//		if (get_company_pref_display('account_tag'))
//			$this->add_lapp_function(2, _(get_company_pref_display('account_tag_text')),
//				"admin/tags.php?type=account", 'SA_GLACCOUNTTAGS', MENU_MAINTENANCE);
//		$this->add_lapp_function(2, "","");
//
//		if (get_company_pref_display('Currencies'))
//			$this->add_lapp_function(2, _(get_company_pref_display('Currencies_text')),
//				"gl/manage/currencies.php?", 'SA_CURRENCY', MENU_MAINTENANCE);
//
//		if (get_company_pref_display('exchange_rates'))
//			$this->add_lapp_function(2, _(get_company_pref_display('exchange_rates_text')),
//				"gl/manage/exchange_rates.php?", 'SA_EXCHANGERATE', MENU_MAINTENANCE);
//
//
//		if (get_company_pref_display('gl_account'))
//			$this->add_rapp_function(2, _(get_company_pref_display('gl_account_text')),
//				"gl/manage/gl_accounts.php?", 'SA_GLACCOUNT', MENU_ENTRY);
//
//
//		if (get_company_pref_display('gl_account_group'))
//			$this->add_rapp_function(2, _(get_company_pref_display('gl_account_group_text')),
//				"gl/manage/gl_account_types.php?", 'SA_GLACCOUNTGROUP', MENU_MAINTENANCE);
//
//		if (get_company_pref_display('gl_account_classes'))
//			$this->add_rapp_function(2, _(get_company_pref_display('gl_account_classes_text')),
//				"gl/manage/gl_account_classes.php?", 'SA_GLACCOUNTCLASS', MENU_MAINTENANCE);
//
//		if (get_company_pref_display('closing'))
//			$this->add_rapp_function(2, _(get_company_pref_display('closing_text')),
//				"gl/manage/close_period.php?", 'SA_GLSETUP', MENU_MAINTENANCE);
//
//		if (get_company_pref_display('revaluation'))
//			$this->add_rapp_function(2, _(get_company_pref_display('revaluation_text')),
//				"gl/manage/revaluate_currencies.php?", 'SA_EXCHANGERATE', MENU_MAINTENANCE);

		$this->add_extensions();
	}
}


