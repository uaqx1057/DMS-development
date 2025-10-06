<input type="text" {{ $attributes->merge(['class' => 'form-control']) }}  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"/>
