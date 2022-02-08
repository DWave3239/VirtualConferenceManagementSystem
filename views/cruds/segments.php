{if="$action == 'list'"}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Name</th>
            <th>Subtitle</th>
            <th>Room Link</th>
            <th>Session Chair</th>
            <th>Delay</th>
            <th>
                <a href="{$_base_}backend/{$url}/add" class="btn btn-primary">Add</a>
            </th>
        </tr>
    </thead>
    <tbody>
    {loop="$list"}
        <tr>
            <td>{$value.name}</td>
            <td>{$value.subtitle}</td>
            <td>{$value.individual_link}</td>
            <td>{$value.full_name}</td>
            <td>{$value.delay}</td>
            <td>
                <a href="{$_base_}backend/{$url}/edit/{$value.segmentId}" class="btn btn-primary">Edit</a>
                <a href="#" data-href="{$_base_}backend/{$url}/delete/{$value.segmentId}" class="btn btn-primary" data-toggle="modal" data-target="#crudDeletionOverlay">Delete</a>
                {if="!empty($value.individual_link)"}<a href="{$_base_}statistics/room/{$value.individual_link}" class="btn btn-primary"><i class="fas fa-chart-bar"></i></a>{/if}
            </td>
        </tr>
    {/loop}
    </tbody>
</table>
{/if}

{if="$action == 'edit' || $action == 'add'"}
<script src="https://cdn.tiny.cloud/1/9m2dw96ypqyhdjcsodmiq0qp0rbc7esgdb1j5e5egz5co6ez/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({selector:'textarea#subtitle'});</script>

<form method="post" action="{$_base_}backend/{$url}/{$action}/{$pkId}/1">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Segment name"{if="$action == 'edit'"} value="{$row.name}"{/if}>
    </div>
    <div class="form-group">
        <label for="subtitle">Subtitle:</label>
        <textarea class="form-control" id="subtitle" name="subtitle" placeholder="Subtitle">{if="$action == 'edit'"}{$row.subtitle}{/if}</textarea>
    </div>
    <div class="form-group">
        <label for="chairId">Sessionchair:</label>
        <select class="form-control" id="chairId" name="chairId">
        {loop="sessionchairs"}
            <option value="{$value.userId}" {if="$action == 'edit' && $value.userId == $row.chairId"}selected{/if}>{$value.full_name} | {$value.username}</option>
        {/loop}
        </select>
    </div>
    <div class="form-group">
        <label for="individual_link">Individual Link:</label>
        <input type="text" class="form-control" id="individual_link" name="individual_link" placeholder="Room link"{if="$action == 'edit' && isset($row['individual_link'])"}value="{$row.individual_link}"{/if}>
    </div>
    <div class="form-group">
        <label for="timeslotId">Timeslot:</label>
        <select class="form-control" id="timeslotId" name="timeslotId">
        {loop="timeslots"}
            <option value="{$value.timeslotId}" {if="$action == 'edit' && $value.timeslotId == $row.timeslotId"}selected{/if}>{$value.date}, {$value.time_from}-{$value.time_until}</option>
        {/loop}
        </select>
    </div>
    <div class="form-group">
        <label for="delay">Segment delay: <small class="text-muted">(HH:mm)</small></label>
        <input type="time" class="form-control" id="delay" name="delay"{if="$action == 'edit' && isset($row['delay'])"} value="{function="substr($row.delay, 0, -3)"}"{/if}>
        <select class="form-control" id="delaySign" name="delaySign">
            <option value="+1" {if="$action == 'edit' && $row.delaySign > 0"}selected{/if}>late</option>
            <option value="-1" {if="$action == 'edit' && $row.delaySign < 0"}selected{/if}>early</option>
        </select>
    </div>
    <div class="form-group">
        <label for="tracks">Tracks this segment is a part of:</label>
        <select class="form-control" id="tracks" name="tracks[]" multiple>
        {loop="$tracks"}
            <option value="{$value.trackId}"{if="$action == 'edit' && in_array($value.trackId, $selectedTracks)"} selected{/if}>{$value.name}</option>
        {/loop}
        </select>
    </div>
    <!-- auths -->
    <div class="form-group">
        <label for="auths">Authorizations needed for this segment:</label>
        <select class="form-control" id="auths" name="auths[]" multiple>
        {loop="$auths"}
            <option value="{$value.authId}"{if="$action == 'edit' && in_array($value.authId, $auths2segments)"} selected{/if}>{$value.name}</option>
        {/loop}
        </select>
    </div>
    <div class="form-group">
        <div class="text-right">
            <button class="btn btn-primary">Save</button>&nbsp;<a href="{$_base_}backend/{$url}" class="btn btn-danger">Cancel</a>
        </div>
    </div>
    <input type="hidden" value="{$token}" name="token"/>
</form>
{/if}