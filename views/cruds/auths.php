{if="$action == 'list'"}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>
                <a href="{$_base_}backend/{$url}/add" class="btn btn-primary">Add</a>
            </th>
        </tr>
    </thead>
    <tbody>
    {loop="$list"}
        <tr>
            <td>{$value.name}</td>
            <td>{$value.description}</td>
            <td>
                <a href="{$_base_}backend/{$url}/edit/{$value.authId}" class="btn btn-primary">Edit</a>
                <a href="#" data-href="{$_base_}backend/{$url}/delete/{$value.authId}" class="btn btn-primary" data-toggle="modal" data-target="#crudDeletionOverlay">Delete</a>
            </td>
        </tr>
    {/loop}
    </tbody>
</table>
{/if}

{if="$action == 'edit' || $action == 'add'"}
<script src="https://cdn.tiny.cloud/1/9m2dw96ypqyhdjcsodmiq0qp0rbc7esgdb1j5e5egz5co6ez/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({selector:'textarea#description'});</script>

<form method="post" action="{$_base_}backend/{$url}/{$action}/{$pkId}/1">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name"{if="$action == 'edit'"} value="{$row.name}"{/if}>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Description">{if="$action == 'edit'"}{$row.description}{/if}</textarea>
    </div>
    <div class="form-group">
        <div class="text-right">
            <button class="btn btn-primary">Save</button>&nbsp;<a href="{$_base_}backend/{$url}" class="btn btn-danger">Cancel</a>
        </div>
    </div>
    <input type="hidden" value="{$token}" name="token"/>
</form>
{/if}