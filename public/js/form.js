/**
 * On Document Load - form.js
 */
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $dtForms = $('#dtForms').DataTable( {
        paging: true,
        destroy: true,
        pageLength: 10,
        order: [],
        bLengthChange: false,
        ajax: {
            type: 'GET',
            url: '/Form/GetForms',
            dataSrc: function (data) {
                console.log(data)
                return data;
            }
        },
        columns: [
            {
                title: 'Created Date', data: null, render: function (data) {
                    return moment(data.created_at).format("YYYY-MM-DD hh:mm:ss A");
                }
            },
            {
                title: 'Request ID', data: null, render: function (data) {
                    return 'Req-' + data.id;
                }
            },
            { title: 'Title', width:'30%', data: 'title' },
            {
                title: 'Due Date', data: null, render: function (data) {
                    return data.status == 'Pending' ? moment(data.duedate).format("YYYY-MM-DD") + ' Days Remaining: (' +  moment(data.duedate).diff(moment(), 'days')  + ')'
                        : moment(data.duedate).format("YYYY-MM-DD");
                }
            },
            {
                title: 'Authorize By', data: null, render: function (data) {
                    return data.name == null ? data.email + ' (Unregistered User)'
                        : data.name + ' (' + data.email + ')';
                }
            },
            { title: 'Status', data: 'status' },
            {
                title: 'Authorize/ Rejected Date', data: null, render: function (data) {
                    return data.status == 'Pending' ? ''
                        : moment(data.authdate).format("YYYY-MM-DD");
                }
            },
        ]
    });

    $('#btnUpload').on('click',function(e){
        getForm(null);
    });

    $("#dtForms tbody").on("click", "tr", function (e) {
        $data = $dtForms.row(this).data();
        //console.log($data)
        getForm($data);
    });

    function getForm(data)
    {
        $('.bladeForm').remove();

        $.get( "/Form/index", { "id": data, }, function(rx) {
            //console.log(rx)
            $('.bladeHome').addClass('d-none');
            $('.bladeHome').parent().append(rx);

        }); // end
    }
});
