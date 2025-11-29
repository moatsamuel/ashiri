<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Register for Asiri</h4>
                </div>
                <div class="card-body p-4">
                    <form wire:submit.prevent="register">
                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" wire:model.blur="name" placeholder="Enter your full name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" wire:model.blur="username" placeholder="Choose a unique username">
                            <small class="form-text text-muted">Letters, numbers, and underscores only. Min 3 characters.</small>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" wire:model.blur="email" placeholder="your@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" wire:model.blur="password" placeholder="Min 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" wire:model.blur="password_confirmation" 
                                   placeholder="Re-enter your password">
                        </div>

                        <!-- Terms -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                                   id="terms" wire:model="terms">
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-primary">Terms and Conditions</a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                <span wire:loading.remove>Register</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Registering...
                                </span>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>