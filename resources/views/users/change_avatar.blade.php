<div id="uploadAvatar{{$user->id}}" class="modal fade" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center">
        <h4 class="modal-title" id="myModalLabel">Change Avatar</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ url('change-avatar/'.$user->id) }}" onsubmit="show()" enctype="multipart/form-data" class="validation-wizard wizard-circle">
        @csrf

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <div class="text-center">

            <input type="file" id="fileInput" accept="image/*" name='file' required>Upload Image

         

       
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-danger-subtle text-danger waves-effect" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn bg-info-subtle text-info waves-effect">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
