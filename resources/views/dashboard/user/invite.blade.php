@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Share Your Invites</h3>

        <p>You have <strong>{{ $remainingInvites }}</strong> invites remaining.</p>

        <form action="{{ route('user.invites.send') }}" method="POST">
            @csrf

            <div id="invite-fields">
                <!-- Email and amount input fields will be appended here -->

                <div class="mb-3 invite-group">
                    <label for="emails[]" class="form-label">Recipient Email</label>
                    <input type="email" name="emails[]" class="form-control" required>

                    <label for="amounts[]" class="form-label mt-2">Number of Invites</label>
                    <input type="number" name="amounts[]" class="form-control" min="1" required>

                    <button type="button" class="btn btn-danger mt-2 remove-field">Remove</button>
                </div>
            </div>

            <button type="button" id="add-field" class="btn btn-primary my-3">Add Another Recipient</button>
            <button type="submit" class="btn btn-success">Send Invites</button>
        </form>
    </div>

    <script>
        document.getElementById('add-field').addEventListener('click', function () {
            const newFieldGroup = document.querySelector('.invite-group').cloneNode(true);
            document.getElementById('invite-fields').appendChild(newFieldGroup);

            newFieldGroup.querySelector('input[name="emails[]"]').value = '';
            newFieldGroup.querySelector('input[name="amounts[]"]').value = '';

            // Add event listener for newly cloned "Remove" button
            newFieldGroup.querySelector('.remove-field').addEventListener('click', function () {
                this.parentElement.remove();
            });
        });

        // Remove field functionality
        document.querySelectorAll('.remove-field').forEach(button => {
            button.addEventListener('click', function () {
                this.parentElement.remove();
            });
        });
    </script>
@endsection
