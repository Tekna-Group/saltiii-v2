<div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-labelledby="editchangeAvatar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editchangeAvatar">Change Avatar</h5>
              
            </div>
           
      <form method="POST" action="{{ url('change-avatar/'.auth()->user()->id) }}" onsubmit="show()" enctype="multipart/form-data" class="validation-wizard wizard-circle">
        @csrf

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <div class="text-center">

            <input type="file" id="fileInput" accept="image/*" name='file' required>

         

       
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
