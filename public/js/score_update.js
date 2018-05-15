	
function update_score_home_p(match_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var formData = {
        match_id : match_id,
        score_h : $("#match-"+match_id+'-result-home').val(),
    }
    $.ajax({
        type: "POST",
        url: window.App.baseurl+'/prognostics/update_score_home',
        data: formData,
        dataType: 'json',
        success: function () {                
        },
        error: function () {   
        }
    });
}  
	
function update_score_away_p(match_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var formData = {
        match_id : match_id,
        score_a : $("#match-"+match_id+'-result-away').val(),
    }
    $.ajax({
        type: "POST",
        url: window.App.baseurl+'/prognostics/update_score_away',
        data: formData,
        dataType: 'json',
        success: function () {                
        },
        error: function () {   
        }
    });
} 

function update_match_score_home(match_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var formData = {
        match_id : match_id,
        score_h : $("#match-"+match_id+'-result-home').val(),
    }
    $.ajax({
        type: "POST",
        url: window.App.baseurl+'/matches/update_score_home',
        data: formData,
        dataType: 'json',
        success: function () {                
        },
        error: function () {   
        }
    });
}  

function update_match_score_away(match_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var formData = {
        match_id : match_id,
        score_a : $("#match-"+match_id+'-result-away').val(),
    }
    $.ajax({
        type: "POST",
        url: window.App.baseurl+'/matches/update_score_away',
        data: formData,
        dataType: 'json',
        success: function () {                
        },
        error: function () {   
        }
    });
} 
    

