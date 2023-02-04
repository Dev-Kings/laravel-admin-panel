@if(session()->has('message'))
<div x-data="{show: true}" x-init="setTimeout(() => show = false, 2000)" x-show="show"
  class="fixed top-10 left-1/2 transform -translate-x-1/2 text-green-800  px-4 py-3">
  <p>
    {{session('message')}}
  </p>
</div>
@endif