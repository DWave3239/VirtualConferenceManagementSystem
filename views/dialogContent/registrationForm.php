<form method="POST" action="{$_base_}main/register" id="registerForm">
    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="title" name="title" placeholder="Title">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="last_name" name="full_name" placeholder="Full name*">
        </div>
    </div>

    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="last_name" name="affiliation" placeholder="Affiliation*">
        </div>
    </div>

    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="country" name="country" placeholder="Country*">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="city" name="city" placeholder="City">
        </div>
    </div>

    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="state" name="state" placeholder="State/Province">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip/Postal Code">
        </div>
    </div>

    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone number">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="fax" name="fax" placeholder="Fax">
        </div>
    </div>

    <div class="row form-group">
        <div class="col">
            <input type="text" class="form-control" id="email" name="email" placeholder="Email*">
        </div>
    </div>

    <div class="form-group">
        <select class="form-control" id="role" name="role" aria-label="Attending as" onchange="showFormStuff(this)">
            <option selected>Choose your Role</option>
            <option value="presenter">Presenter</option>
            <option value="non_presenter">Non-Presenter</option>
        </select>
    </div>

    <div class="presenter" style="display: none">
        <p><small>Up to ... extra pages are allowed but will be billed.</small></p>
        <div class="row form-group">
            <div class="col">
                <input type="text" class="form-control" id="paperId" name="paperId" placeholder="Paper ID" onkeyup="checkSubmit(this)">
            </div>
        </div>
        <h6>Additional papers:</h6>
        <div class="row form-group">
            <div class="col">
                <input type="text" class="form-control" id="additional_paper1" name="additional_paper1" placeholder="Additional paper ID 1">
            </div>
        </div>
        <div class="row form-group">
            <div class="col">
                <input type="text" class="form-control" id="additional_paper2" name="additional_paper2" placeholder="Additional paper ID 2">
            </div>
        </div>
        <div class="row form-group">
            <div class="col">
                <input type="text" class="form-control" id="additional_paper3" name="additional_paper3" placeholder="Additional paper ID 3">
            </div>
        </div>
        Number of additional people:
        <div class="row form-group">
            <div class="col">
                <input type="text" class="form-control" id="additional_people" name="additional_people" placeholder="Number of additional people">
            </div>
        </div>
        <button type="submit" class="btn btn-primary disabled submit" disabled>Register{if="$early_bird"} (early bird price){/if}</button>
    </div>
    <div class="non_presenter" style="display: none">
        <button type="submit" class="btn btn-primary submit">Register{if="$early_bird"} (early bird price){/if}</button>
    </div>
</form>
<script>
    function showFormStuff(e){
        $('#registerForm .presenter').hide();
        $('#registerForm .non_presenter').hide();
        if($('#role').val()){
            $('#registerForm .'+$('#role').val()).show();
        }
    }

    function checkSubmit(e){
        if($(e).val()){
            $('#registerForm .presenter .submit').removeClass('disabled');
            $('#registerForm .presenter .submit').removeAttr('disabled');
        }else{
            $('#registerForm .presenter .submit').addClass('disabled');
            $('#registerForm .presenter .submit').attr('disabled', 'disabled');
        }
    }
</script>