(function( $ ) {
	'use strict';
	function format_search_results(response){
		let result = '<p><em>Searching for:</em> &quot;' + response.searchstring + '&quot;</p>';
		let result_count = 0;
		for (const [user_id, item] of Object.entries(response.data)) {
			result += '<div class="row pb-2">';
			result_count++;
			result += '<div class="col-md-6"><strong>' + item.LegalName + ' (' + item.EmployeeID + ')</strong><br>';
			result += item.RankName + ' in ' + item.UnitName + ' (' + item.AppointmentType + ')</div>';
			result += '<div class="col-md-2">';
			if (item.CaseID == '' ) {
				result += '<a href="' + my_ajax_obj.init_url + '=' + item.UWODSAppointmentTrackKey + '" class="btn btn-outline-primary">Initiate case</a>';
			}
			else {
				result += '<a href="' + my_ajax_obj.case_url + '=' + item.CaseID + '" class="btn btn-outline-primary">Details</a>';
			}
			result += '</div>';
			result += '</div>';
		}
		if (result_count == 0) {
			result += '<p><em>None found</em></p>';
		}
		return result;
	}

	$(document).ready(function() {
		$('#TargetRankKey').change(function(){
			var val = $('#TargetRankKey option:selected').val();
//			alert('new rank ' + val);
			if ( val != 0 ) {
				$('#actiontype-display').html($('#TargetRankKey option:selected').data('actiontype'));
			}
			else {
				$('#actiontype-display').text('');
			}
		});
		$('#rptinfo_search').on('keyup', function(event){
			let val = $('#rptinfo_search').val();
			if (val.length > 3) {
//				alert('Search for ' + val);
				let this2 = this;                  //use in callback
				$.post(my_ajax_obj.ajax_url, {      //POST request
						_ajax_nonce: my_ajax_obj.nonce, //nonce
						action: "rpt_info_candidate_search",         //action
						searchstring: val,
						user_id: my_ajax_obj.user_id,
						template_type: my_ajax_obj.template_type
					}, function(response) {            //callback
						$('#rptinfo_search_results').html(format_search_results(response));
					}
				);
			}
		});
		$('#UnitSelect').change(function () {
			let link = $('#UnitSelect option:selected').data('link');
			window.open(link, "_self");
		});
		$('#rpt-quarter-select-group').change(function() {
			let ccount = $('.QtrYes:checked').length;
			$('#QtrCount').text(ccount);
			switch (ccount) {
				case 1:
					$('#SalarySupportPct').val('100%');
					break;
				case 2:
					$('#SalarySupportPct').val('75%');
					break;
				case 3:
					$('#SalarySupportPct').val('67%');
					break;
				default:
					$('#SalarySupportPct').val('');
					break;
			}
		});
        $('#CycleAcademicYear').change(function(){
            $('#PromotionSubmissionStartDate').val($('#CycleAcademicYear option:selected').data('promotionsubmissionstartdate'));
            $('#PromotionSubmissionEndDate').val($('#CycleAcademicYear option:selected').data('promotionsubmissionenddate'));
            $('#SabbaticalCompLimit').val($('#CycleAcademicYear option:selected').data('sabbaticalcomplimit'));
            $('#SabbaticalSubmissionStartDate').val($('#CycleAcademicYear option:selected').data('sabbaticalsubmissionstartdate'));
            $('#SabbaticalSubmissionEndDate').val($('#CycleAcademicYear option:selected').data('sabbaticalsubmissionenddate'));
        });
        $('#SabbaticalAcademicYear').change(function(){
            $('#Unit-175').val($('#SabbaticalAcademicYear option:selected').data('175'));
            $('#Unit-169').val($('#SabbaticalAcademicYear option:selected').data('169'));
            $('#Unit-181').val($('#SabbaticalAcademicYear option:selected').data('181'));
            $('#Unit-16').val($('#SabbaticalAcademicYear option:selected').data('16'));
            $('#Unit-178').val($('#SabbaticalAcademicYear option:selected').data('178'));
            $('#Unit-153').val($('#SabbaticalAcademicYear option:selected').data('153'));
            $('#Unit-152').val($('#SabbaticalAcademicYear option:selected').data('152'));
            $('#Unit-195').val($('#SabbaticalAcademicYear option:selected').data('195'));
            $('#Unit-86').val($('#SabbaticalAcademicYear option:selected').data('86'));
            $('#Unit-62').val($('#SabbaticalAcademicYear option:selected').data('62'));
            $('#Unit-59').val($('#SabbaticalAcademicYear option:selected').data('59'));
            $('#Unit-126').val($('#SabbaticalAcademicYear option:selected').data('126'));
            $('#Unit-133').val($('#SabbaticalAcademicYear option:selected').data('133'));
            $('#Unit-183').val($('#SabbaticalAcademicYear option:selected').data('183'));
            $('#Unit-58').val($('#SabbaticalAcademicYear option:selected').data('58'));
            $('#Unit-17').val($('#SabbaticalAcademicYear option:selected').data('17'));
            $('#Unit-164').val($('#SabbaticalAcademicYear option:selected').data('164'));
            $('#Unit-118').val($('#SabbaticalAcademicYear option:selected').data('118'));
        });
		$('#rptinfo_case_form').submit(function(e){
			if( !confirm('Are you sure you want to submit the case?') ){
				e.preventDefault();
			}
		});
		if ( $.fn.dataTable.isDataTable( '.sort-table' ) ) {
			var rpttable = $('.sort-table').DataTable();
		}
		else {
			var rpttable = $('.sort-table').DataTable({
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"pageLength": 50,
				"aaSorting": [[1, 'asc' ],[2, 'asc']],
				"columnDefs": [ {
					"targets"  : 'no-sort',
					"orderable": false
				}],
				"oLanguage": {
					"sSearch": "Filter: "
				},
				"layout": {
                    "topStart": {
                        "buttons": ["csv", "excel"]
                    }
                }
			});
		}
	});
})( jQuery );
