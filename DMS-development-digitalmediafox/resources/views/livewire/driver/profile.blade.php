<div class="mobile-wrapper">
    <x-layouts.driverheader />

    <style>
        html, body {
            height: 100%;
            overscroll-behavior-y: contain; /* disables pull-to-refresh but keeps scroll bounce */
        }

        .activeli {
            color: green;
        }

        .profile-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid #712b80;
            border-radius: 40px;
            padding: 11px;
        }

        .w-45 {
            width: 45%;
        }

        .bodySection {
            margin-bottom: 100px;
        }
    </style>

    <div class="container bodySection">
        <div class="card shadow-sm rounded-4 p-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold">
                     @if($editMode)
                     Edit Profile
                     @else
                     Profile
                     @endif
                </h5>

                @if($editMode)
                   
                @else
                    <a href="javascript:void(0)" wire:click="toggleEdit" class="btn btn-sm btn-primary">
                        <i class="ri-edit-line me-1"></i> Edit
                    </a>
                @endif
            </div>

            <!-- Profile Image & Name -->
            <div class="text-center mb-3">
                <div class="profile-circle bg-light mx-auto mb-2">
                
                        @if($image)
                            <img src="{{ asset('storage/app/public/livewire-tmp/' . $image->getFilename()) }}" class="rounded-circle" width="80" height="80">
                        
                        
                        @else
                        @if($driver->image)
                            <img src="{{ asset('storage/app/public/' . $driver->image) }}" alt="{{ $driver->name }}" class="rounded-circle" width="80" height="80">
                        @else
                            <span class="text-white fs-3 fw-bold">{{ strtoupper(substr($driver->name, 0, 1)) }}</span>
                        @endif
                        @endif
                    
                </div>
                @if($editMode)
                <!-- Hidden file input -->
            <input type="file" id="imageUpload" wire:model="image" style="display: none;">
            
            <!-- Custom button to trigger the file input -->
            <button type="button" class="btn btn-sm btn-primary mt-1" onclick="document.getElementById('imageUpload').click()">
                Change image
            </button>

                @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                @endif
                
                
                
                
            </div>

            <!-- Stats -->
            <div class="d-flex justify-content-around text-center mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 w-45">
                    <h6 class="mb-0 text-success fw-bold">{{ $totalOrders }}</h6>
                    <small>Total Deliveries</small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 w-45">
                    <h6 class="mb-0 text-primary fw-bold">0.0</h6>
                    <small>Rating</small>
                </div>
            </div>

            <!-- Profile Details -->
            <ul class="list-group list-group-flush">

                <!-- Name -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Name</small>
                        @if($editMode)
                            <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="Name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        @else
                            <div>{{ $name }}</div>
                        @endif
                    </div>
                    <i class="ri-user-line fs-5 text-secondary"></i>
                </li>

                <!-- Mobile -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Phone</small>
                        @if($editMode)
                            <input type="text" wire:model="mobile" class="form-control form-control-sm" placeholder="Mobile">
                            @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                        @else
                            <div>{{ $mobile }}</div>
                        @endif
                    </div>
                    <i class="ri-phone-line fs-5 text-secondary"></i>
                </li>

                <!-- Nationality -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Nationality</small>
                        @if($editMode)
                            <input type="text" wire:model="nationality" class="form-control form-control-sm" placeholder="Nationality">
                            @error('nationality') <span class="text-danger">{{ $message }}</span> @enderror
                        @else
                            <div>{{ $nationality ? $nationality : 'Not Found' }}</div>
                        @endif
                    </div>
                    <i class="ri-map-pin-line fs-5 text-secondary"></i>
                </li>

                <!-- Language -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Language</small>
                        @if($editMode)
                            <input type="text" wire:model="language" class="form-control form-control-sm" placeholder="Language">
                            @error('language') <span class="text-danger">{{ $message }}</span> @enderror
                        @else
                            <div>{{ $language ? $language : 'Not Found'}}</div>
                        @endif
                    </div>
                    <i class="ri-bar-chart-line fs-5 text-secondary"></i>
                </li>

                <!-- Branch (not editable) -->
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Branch</small>
                        <div>{{ $driver->branch ? $driver->branch->name : 'No Branch' }}</div>
                    </div>
                    <i class="ri-building-line fs-5 text-secondary"></i>
                </li>

            </ul>

             @if($editMode)
             <div class="d-flex mt-4">
                <button wire:click="saveProfile" class="btn btn-outline-success  me-1 w-50">
                <i class="ri-save-line"></i> Save
                </button>
                <button wire:click="toggleEdit" class="btn  btn-outline-danger w-50">
                <i class="ri-close-line"></i> Cancel
                </button>
             </div>
             
             @else
            <!-- Logout -->
            <a href="logout" class="btn btn-sm btn-outline-danger py-2 mt-4">
                <i class="ri-logout-box-r-line"></i> Logout
            </a>
            @endif
        </div>
    </div>

    <x-layouts.driverfooter />
</div>
