@extends('layouts.app')
<style>
  .padTop10 {
    padding-top: 10px;
  }
  .txt-area {
    resize: none;
    height: 150px;
  }
</style>
@section('content')

<div class="container">
  <div class="bladeForm row justify-content-center" data-user="{{ $user_id }}">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Review & Authorization Panel</div>

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
                              <input type="text" id="duedate" name="duedate"  class="form-control" value="{{ $form != null ? $form->duedate : '' }}" readonly/>
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
                              <input id="email" name="email"  class="form-control" value="{{ $form != null ? $form->email : '' }}" readonly/>
                          </div>
                      </div>
                      <div class="row padTop10">
                          <div class="col-md-12">
                              <label>Title</label>
                              <input id="title" name="title"  class="form-control" value="{{ $form != null ? $form->title : '' }}" readonly/>
                          </div>
                      </div>
                      <div class="row padTop10">
                          <div class="col-md-12">
                              <label>Description</label>
                              <textarea id="description" name="description" class="form-control txt-area" readonly>{{ $form != null ? $form->description : '' }}</textarea>
                          </div>
                      </div>
                      <div class="row padTop10">
                          <div class="col-md-12">
                              <label>Reason/ Comment</label>
                              <textarea id="reason" name="reason" class="form-control txt-area">{{ $form != null ? $form->reason : '' }}</textarea>
                          </div>
                      </div>
                      <div class="row padTop10">
                          <div class="col-md-12">
                              <label>Attachment</label><br/>
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
                              <input type="button" class="btn btn-danger btn-md" id="btnReject" value="Reject">
                              <input type="button" class="btn btn-success btn-md float-right" id="btnAuth" value="Authorize">
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  $(function() {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      // init update handling
      if ($('#status').val() != 'Pending')
          $('.btn, .txt-area').prop('disabled', true);

      function updateForm(data)
      {
        $.ajax({
            url: '/Form/update',
            type: 'POST',
            data: data,
            success: function(data, textStatus, jqXHR)
            {
              new PNotify({
                  title: 'Success',
                  text: 'Update Successfully!',
                  type: 'success',
                  styling: 'bootstrap3'
              });

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
      }

      /** buttons on click handling **/
      $('#btnReject').on('click',function(e){
        if($('#reason').val() != '')
        {
          var data = {};
          $(".form-control").each(function() {
              var $name = $(this).attr('name');
              data[$name] = $(this).val();
          });

          data.user_id = $('.bladeForm').data('user');
          data.status = 'Rejected';

          updateForm(data);
          $('.btn, .txt-area').prop('disabled', true);
        }
        else {
          new PNotify({
              title: 'Warning',
              text: 'Please input the reason', // warning msg
              type: 'warning',
              styling: 'bootstrap3'
          });
        }
      });

      Dropzone.autoDiscover = false;
      var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
          

      });
  });
</script>
@endsection
