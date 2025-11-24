@csrf

<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control">
    @if(isset($user))
        <small>Leave blank to keep current password.</small>
    @endif
</div>

<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-control" required>
        <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
</div>

<button class="btn btn-success">Save</button>
