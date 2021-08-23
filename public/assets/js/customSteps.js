let obj = {
    user: {},
    sub_users: {},
    party_name: '',
    party_address: '',
    inventories: {},
    sub_inventories: {}
}

$(document).ready(function(){
    $('.select2').select2();

    if(cart_id != ''){
        _cart_detail();
    }

    _users();

    $('#user').change(function(){
        obj.user = {};

        let id = $(this).val();
        _sub_users(id);
    });

    $(document).on('click', '#inventories_pagination .pagination a', function(event){
        event.preventDefault(); 
        var page = $(this).attr('href').split('page=')[1];
        var search = $('#inventories_search').val();
        _inventories(page, search);
    });

    $(document).on('keyup', '#inventories_search', function(event){
        event.preventDefault(); 
        var search = $('#inventories_search').val();
        _inventories(0, search);
    });

    $(document).on('click', '.inventories', function(){
        var value = $(this).val();
        var name = $(this).data('name');
        var item = $(this).data('item');
        
        if($(this).prop("checked") == true){
            obj.inventories[value] = {'name': name, 'item': item};
        } else {
            if(cart_id != ''){
                $.ajax({
                    "url": config.routes.delete_inventories+"?cart_id="+cart_id+"&id="+value,
                    "dataType": "json",
                    async: false,
                    cache: false,
                    "type": "Get",
                    success: function (response){
                        if(response.code == 200){
                            delete obj.inventories[value];
                        } else {
                            toastr.error(['Something went wrong, please try again later', 'Error']);
                        }
                    },
                    error: function(response){
                        toastr.error(['Something went wrong, please try again later', 'Error']);
                    }
                });
            }else{
                delete obj.inventories[value];
            }
        }
    });

    $(document).on('click', '#sub_inventories_pagination .pagination a', function(event){
        event.preventDefault(); 
        var page = $(this).attr('href').split('page=')[1];
        var search = $('#sub_inventories_search').val();
        _sub_inventories(page, search);
    });

    $(document).on('keyup', '#sub_inventories_search', function(event){
        event.preventDefault(); 
        var search = $('#sub_inventories_search').val();
        _sub_inventories(0, search);
    });

    $(document).on('click', '.sub_inventories', function(event){
        var value = $(this).val();
        var name = $(this).data('name');
        var item = $(this).data('item');

        if($(this).prop("checked") == true){
            obj.sub_inventories[value] = {'name': name, 'item': item};
        } else {
            if(cart_id != ''){
                $.ajax({
                    "url": config.routes.delete_sub_inventories+"?cart_id="+cart_id+"&id="+value,
                    "dataType": "json",
                    async: false,
                    cache: false,
                    "type": "Get",
                    success: function (response){
                        if(response.code == 200){
                            delete obj.sub_inventories[value];
                        } else {
                            toastr.error(['Something went wrong, please try again later', 'Error']);
                        }
                    },
                    error: function(response){
                        toastr.error(['Something went wrong, please try again later', 'Error']);
                    }
                });
            }else{
                delete obj.sub_inventories[value];
            }
        }
    });

    var btnFinish = $('<button></button>').text('Finish')
                                        .addClass('btn btn-info disabled')
                                        .attr('id', 'finish')
                                        .on('click', function(){ 
                                            if(cart_id != ''){
                                                obj['cart_id'] = cart_id
                                            }
    
                                            $.ajaxSetup({
                                                headers: {
                                                  'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                                                }
                                            });
                                            $.ajax({
                                                "url": config.routes.insert,
                                                "dataType": "json",
                                                "type": "post",
                                                "data": {
                                                    obj
                                                },
                                                success: function (response){
                                                    if(response.code == 200){
                                                        toastr.success(response.message, 'Success');
                                                        setTimeout(function(){ window.location.replace(config.routes.cart); }, 2000);
                                                    } else {
                                                        toastr.error(response.message, 'Error');
                                                        setTimeout(function(){ location.reload(); }, 2000);
                                                    }
                                                },
                                                error: function(response){
                                                    if(response.status === 422) {
                                                        var errors_ = response.responseJSON;
                                                        $.each(errors_, function (key, value) {
                                                            toastr.error(value, 'Error');
                                                        });
    
                                                        setTimeout(function(){ location.reload(); }, 2000);
                                                    }
                                                }
                                            });
                                        });
    
    $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
        var time = 500;

        $('#smartwizard').smartWizard("loader", "show");

        if(stepPosition === 'first'){
            $("#finish").addClass('disabled');
        }

        var repo = anchorObject.data('repo');

        if(stepPosition === 'forward' && repo == '0'){
            var user = $("#user option:selected").val();    
            var name = $("#user option:selected").text();
            
            if(user != ''){
                obj.user[user] = name;
            }
            
            obj.sub_users = {}
            var sub_users = $('#sub_users').select2('data')
            sub_users.forEach(function(item) {
                obj.sub_users[item.id] = item.text
            });

            obj.party_name = $('#party_name').val();
            obj.party_address = $('#party_address').val();

            $('.user_id, .sub_users, .party_name, .party_address').html('');

            if(jQuery.isEmptyObject(obj.user) || jQuery.isEmptyObject(obj.sub_users) || obj.party_name == '' || obj.party_address == ''){
                if(jQuery.isEmptyObject(obj.user)){
                    $('.user').html('please select user')
                } else if(jQuery.isEmptyObject(obj.sub_users)){
                    $('.sub_users').html('please select sub users')
                } else if(obj.party_name == ''){
                    $('.party_name').html('please enter party name')
                } else if(obj.party_address == ''){
                    $('.party_address').html('please enter party address')
                }
                $('#smartwizard').smartWizard("loader", "hide");
                return false
            }else{
                $('#smartwizard').smartWizard("loader", "hide");
                _inventories(0, '');
                return true;
            }
        } else if(stepPosition === 'forward' && repo == '1'){
            if(jQuery.isEmptyObject(obj.inventories)){
                $('.inventory_error').html('please select inventory')
                $('#smartwizard').smartWizard("loader", "hide");
                return false;
            } else {
                $('.inventory_error').html('')
                $('#smartwizard').smartWizard("loader", "hide");
                _sub_inventories(0, '');
                return true;
            }
        } else if(stepPosition === 'forward' && repo == '2'){
            if(jQuery.isEmptyObject(obj.sub_inventories)){
                $('.sub_inventory_error').html('please select sub inventory')
                $('#smartwizard').smartWizard("loader", "hide");
                return false;
            } else {
                $('.sub_inventory_error').html('')
                $('#smartwizard').smartWizard("loader", "hide");
                $("#finish").removeClass('disabled');
                _preview();
                return true;
            }
        }

        $('#smartwizard').smartWizard("loader", "hide");
    });

    $('#smartwizard').smartWizard({
        selected: 0, // Initial selected step, 0 = first step
        theme: 'arrows', // theme for the wizard, related css need to include for other than default theme
        justified: true, // Nav menu justification. true/false
        darkMode: false, // Enable/disable Dark Mode if the theme supports. true/false
        autoAdjustHeight: false, // Automatically adjust content height
        cycleSteps: false, // Allows to cycle the navigation of steps
        backButtonSupport: true, // Enable the back button support
        enableURLhash: true, // Enable selection of the step based on url hash
        transition: {
            animation: 'none', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
            speed: '400', // Transion animation speed
            easing:'' // Transition animation easing. Not supported without a jQuery easing plugin
        },
        toolbarSettings: {
            toolbarPosition: 'bottom', // none, top, bottom, both
            toolbarButtonPosition: 'right', // left, right, center
            showNextButton: true, // show/hide a Next button
            showPreviousButton: true, // show/hide a Previous button
            toolbarExtraButtons: [btnFinish] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
        },
        anchorSettings: {
            anchorClickable: true, // Enable/Disable anchor navigation
            enableAllAnchors: false, // Activates all anchors clickable all times
            markDoneStep: true, // Add done state on navigation
            markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
            enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
        },
        keyboardSettings: {
            keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
            keyLeft: [37], // Left key code
            keyRight: [39] // Right key code
        },
        lang: { // Language variables for button
            next: 'Next',
            previous: 'Previous'
        },
        disabledSteps: [], // Array Steps disabled
        errorSteps: [], // Highlight step with errors
        hiddenSteps: [] // Hidden steps
    });

    if(jQuery.isEmptyObject(obj.user)){
        $('#smartwizard').smartWizard("reset");
    }
});

function _users(){
    $.ajax({
        "url": config.routes.users+'?cart_id='+cart_id,
        "dataType": "json",
        "type": "Get",
        success: function (response){
            if(response.code == 200){
                $('#user').html(response.data);            
            }else{
                $('#user').html('<option value="">Select user</option>');            
            }

            if(cart_id != ''){
                _sub_users(Object.keys(obj.user)[0]);
            }
        },
        error: function(response){
            $('#user').html('<option value="">Select user</option>'); 
        }
    });
}

function _sub_users(id){
    $.ajax({
        "url": config.routes.sub_users+'?id='+id+'&cart_id='+cart_id,
        "dataType": "json",
        "type": "Get",
        success: function (response){
            $('#sub_users').empty();
            
            if(response.code == 200){
                $('#sub_users').html(response.data);            
            }else{
                $('#sub_users').html('');            
            }

            if(cart_id != ''){
                $('#party_name').val(obj.party_name);
                $('#party_address').val(obj.party_address);
            }
        },
        error: function(response){
            $('#sub_users').empty();
        }
    });
}

function _inventories(page, search){
    $.ajax({
        "url": config.routes.inventories+"?page="+page+"&search="+search+"&selected="+JSON.stringify(obj.inventories)+"&cart_id="+cart_id,
        "dataType": "json",
        "type": "Get",
        success: function (response){
            $('#inventories_datatable').html(response.data);
            $('#inventories_pagination').html(response.pagination);
        },
        error: function(response){
            $('#inventories_datatable').html('<td colspan="3" class="text-center"><h3>No data found</h3></td>');
            $('#inventories_pagination').html('');
        }
    });
}

function _sub_inventories(page, search){
    $.ajax({
        "url": config.routes.sub_inventories+"?page="+page+"&search="+search+"&selected="+JSON.stringify(obj.sub_inventories)+"&cart_id="+cart_id,
        "dataType": "json",
        "type": "Get",
        success: function (response){
            $('#sub_inventories_datatable').html(response.data);
            $('#sub_inventories_pagination').html(response.pagination);
        },
        error: function(response){
            $('#sub_inventories_datatable').html('<td colspan="3" class="text-center"><h3>No data found</h3></td>');
            $('#sub_inventories_pagination').html('');
        }
    });
}

function _preview(){
    $('#preview_user').html('<h6>'+obj.user[Object.keys(obj.user)[0]]+'</h6>');
    $('#preview_party_name').html('<h6>'+obj.party_name+'</h6>');
    $('#preview_party_address').html('<h6>'+obj.party_address+'</h6>');

    var sub_users = '';
    $.each(obj.sub_users, function(index, value) {
        sub_users += '<h6>'+value+'</h6>';
    });

    var inventories = '';
    $.each(obj.inventories, function(index, value) {
        inventories += '<div class="row"><div class="col-sm-6"><h6>'+value.name+'</h6></div><div class="col-sm-6"><h6>'+value.item+'</h6></div></div>';
    });

    var sub_inventories = '';
    $.each(obj.sub_inventories, function(index, value) {
        sub_inventories += '<div class="row"><div class="col-sm-6"><h6>'+value.name+'</h6></div><div class="col-sm-6"><h6>'+value.item+'</h6></div></div>';
    });

    $('#preview_sub_users').html(sub_users);
    $('#preview_inventories').html(inventories);
    $('#preview_sub_inventories').html(sub_inventories);
}

function _cart_detail(){
    $.ajax({
        "url": config.routes.detail+"?id="+cart_id,
        "dataType": "json",
        "type": "Get",
        "async": false,
        "cache": false,
        success: function (response){
            if(response.code == 200){
                obj = response.data;
            }else{
                toastr.error(['Something went wrong, please try again later', 'Error']);    
            }
        },
        error: function(response){
            toastr.error(['Something went wrong, please try again later', 'Error']);
        }
    });
}