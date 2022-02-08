{if="$action == 'list'"}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Name</th>
            <th>Segment</th>
            <th>Presenter</th>
            <th>
                {if="$auth_level == $auth_level_administrator"}<a href="{$_base_}backend/{$url}/add" class="btn btn-primary">Add</a>{/if}
            </th>
        </tr>
    </thead>
    <tbody>
    {loop="$list"}
        <tr>
            <td>{$value.name}</td>
            <td>{$value.segmentName}{if="$value.subtitle"} | {$value.subtitle}{/if}</td>
            <td>{$value.full_name}</td>
            <td>
                <a href="{$_base_}backend/{$url}/edit/{$value.paperId}" class="btn btn-primary">Edit</a>
                {if="$auth_level == $auth_level_administrator"}<a href="#" data-href="{$_base_}backend/{$url}/delete/{$value.paperId}" class="btn btn-primary" data-toggle="modal" data-target="#crudDeletionOverlay">Delete</a>{/if}
            </td>
        </tr>
    {/loop}
    </tbody>
</table>
{/if}

{if="$action == 'edit' || $action == 'add'"}
<form method="post" action="{$_base_}backend/{$url}/{$action}/{$pkId}/1">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Title of paper"{if="$action == 'edit'"} value="{$row.name}"{/if}>
    </div>
    <div class="form-group">
        <label for="country">Country:</label>
        <input type="text" class="form-control" id="country" name="country" placeholder="Country"{if="$action == 'edit'"} value="{$row.country}"{/if}>
    </div>
    
    <div class="form-group">
        <label for="uniqueId">Unique ID:</label>
        <input type="text" class="form-control" id="uniqueId" name="uniqueId" placeholder="Unique ID"{if="$action == 'edit'"} value="{$row.uniqueId}"{/if}>
    </div>
    
    <!-- list of authors -->
    <div class="form-group">
        <label for="authors">Authors of this paper:</label>
        <select class="form-control" id="authors" name="authors[]" multiple>
        {loop="$authors"}
            <option value="{$value.userId}"{if="$action == 'edit' && in_array($value.userId, $team)"} selected{/if}>{$value.full_name}</option>
        {/loop}
        </select>
    </div>

    <!-- presenter of paper -->
    <div class="form-group">
        <label for="userId">Presenter of this paper:</label>
        <select class="form-control" id="userId" name="userId">
            <option>Please select authors</option>
        {loop="$authors"}
            <option value="{$value.userId}"{if="$action == 'edit' && $value.userId == $row.userId"} selected{/if}{if="$action != 'edit' || !in_array($value.userId, $team)"} hidden{/if}>{$value.full_name}</option>
        {/loop}
        </select>
    </div>

    <!-- segmentId -->
    <div class="form-group">
        <label for="segmentId">Segment of this paper:</label>
        <select class="form-control" id="segmentId" name="segmentId">
        {loop="$segments"}
            <option value="{$value.segmentId}"{if="$action == 'edit' && $value.segmentId == $row.segmentId"} selected{/if}>{$value.name}{if="$value.subtitle"} | {$value.subtitle}{/if}</option>
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