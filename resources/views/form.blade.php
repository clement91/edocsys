<style>
  .padTop10 {
    padding-top: 10px;
  }
  .txt-area {
    resize: none;
    height: 150px;
  }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="bladeForm row justify-content-center" data-user="{{ $user_id }}">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Upload/ Edit Panel</div>

            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Owner/ Requestor</label>
                            <input id="user" name="user"  class="form-control" value="{{ $user_name }}" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label>Created On</label>
                            <input type="text" id="created_at" name="created_at"  class="form-control" value="{{ $form != null ? $form->created_at : '' }}" readonly/>
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-6">
                            <label>Request ID</label>
                            <input id="txtDocumentID" name="doc_id"  class="form-control" value="{{ $form != null ? 'Req-'. $form->id : '' }}" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label>Due Date</label>
                            <input type="text" id="duedate" name="duedate"  class="form-control" value="" />
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-6">
                            <label>Status</label>
                            <input id="status" name="status"  class="form-control" value="{{ $form != null ? $form->status : '' }}" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label>Authorized/ Rejected Date</label>
                            <input type="text" id="authdate" name="authdate"  class="form-control" value="{{ $form != null ? $form->status != 'Pending' ? $form->authdate : '' : '' }}" readonly/>
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-12">
                            <label>To authorize</label>
                            <input id="email" name="email" class="form-control" value="{{ $form != null ? $form->email : '' }}" placeholder="Enter email address.."/>
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-12">
                            <label>Title</label>
                            <input id="title" name="title"  class="form-control" value="{{ $form != null ? $form->title : '' }}"/>
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="description" name="description" value="" class="form-control txt-area">{{ $form != null ? $form->description : '' }}</textarea>
                        </div>
                    </div>
                    <div class="row padTop10">
                        <div class="col-md-12">
                            <label>Attachment</label><br/>
                            <button id="clickable" class="btn btn-primary btn-md">Select document</button>
                            <div class="container">
                                <div id="previews" class="dropzone-previews">
                                  @foreach($docs as $doc)
                                    <div class="dz-preview dz-file-preview dz-processing dz-complete">
                                      <div class="dz-image"><img data-dz-thumbnail=""></div>
                                      <div class="dz-details row">
                                        <div class="dz-size col-md-2">
                                          <span data-dz-size=""><strong>{{ $doc["size"] }}</strong></span>
                                        </div>
                                        <div class="dz-filename col-md-10">
                                          <span data-dz-name="">
                                            <a href="{{ $doc["path"] }}" class="remove-thing">{{ $doc["name"] }}</a>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  @endforeach
                                </div>
                                <div id="docPath" type="hidden" value="{{ $form != null ? $form->path : '' }}"></div>
                            </div>

                        </div>
                    </div>
                    <div class="row padTop10"></div>
                    <div class="row padTop10"></div>
                    <hr/>
                    <div class="row padTop10">
                        <div class="col-md-12">
                            <input type="button" class="btn btn-danger btn-md" id="btnCancel" value="Cancel/ Return">
                            <input type="button" class="btn btn-primary btn-md float-right" id="btnSubmit" value="Submit">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- date range picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
  $(function() {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      /** timer function **/
      function wait(ms){
         var start = new Date().getTime();
         var end = start;
         while(end < start + ms) {
           end = new Date().getTime();
        }
      }

      /** date range init handling **/
      $('#duedate').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          locale: {
              format: 'YYYY-MM-DD',
        },
          maxYear: parseInt(moment().format('YYYY'),10)
      }, function(start, end, label) {
        //var years = moment().diff(start, 'years');
        //console.log("You are " + years + " years old!");
      });

      /** buttons on click handling **/
      $('#btnCancel').on('click',function(e){
          $('.bladeForm').remove();
          $('.bladeHome').removeClass('d-none');
      });
      $('#btnSubmit').on('click',function(e){
          // loop each input
          var data = {};
          $(".form-control").each(function() {
              var $name = $(this).attr('name');
              data[$name] = $(this).val();

          });
          data.user_id = $('.bladeForm').data('user');
          data.path = $('#docPath').val() == null ? '' : $('#docPath').val();

          // form auth
          data.status = 'Pending';
          data.reason = '';

          // emaill purpose
          data.message = data.title;
          data.name = data.user;

          console.log(data)

          $.ajax({
              url: '/Form/update',
              type: 'POST',
              data: data,
              success: function(data, textStatus, jqXHR)
              {
                new PNotify({
                    title: 'Success',
                    text: 'Submit Successfully!',
                    type: 'success',
                    styling: 'bootstrap3'
                });

                wait(3000);
                location.reload();
              },
              error: function(jqXHR, textStatus, errorThrown)
              {
                  // Handle errors here
                  //console.log('ERRORS: ' + textStatus);
                  new PNotify({
                      title: 'Warning',
                      text: textStatus, // warning msg
                      type: 'warning',
                      styling: 'bootstrap3'
                  });
              }
          });

      });

      Dropzone.autoDiscover = false;
      var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
          url: "/Form/upload", // Set the url
          previewsContainer: "#previews", // Define the container to display the previews
          clickable: "#clickable", // Define the element that should be used as click trigger to select files.
          sending: function(file, xhr, formData) {
              $('.dz-progress, .dz-error-message, .dz-success-mark, .dz-error-mark').remove();
              $('.dz-details').addClass('row');

              $('.dz-filename').addClass('col-md-10');
              $('.dz-size').addClass('col-md-2');

              var _nf = moment().format('YYYYMMDDhhmmss') + '-' + $('.bladeForm').data('user');

              formData.append("_token", "{{ csrf_token() }}");
              formData.append("folder", $('#docPath').val() == '' ? _nf : $('#docPath').val());
              $('#docPath').val() == '' ? $('#docPath').val(_nf) : $('#docPath').val();
          },
          init: function () {
            this.on("complete", function (file) {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              }
            });
          },
          success: function(file, response){
              console.log(response)
              $('span:contains("' + response.filename + '")')
                .wrapInner('<a href="' + response.path + '" class="remove-thing"></a>')

          }
      });

  });
</script>
