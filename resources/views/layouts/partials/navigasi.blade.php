<div class="container mt-4 d-flex justify-content-center">
  <div class="nav-container d-flex justify-content-center flex-wrap w-100" 
       style="border-radius:.5rem; max-width:1300px;">
    
    <a href="{{ route('dosen.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('dosen*')) active @endif" 
       role="button">Dosen</a>
       
    <a href="{{ route('mahasiswa.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('mahasiswa*')) active @endif" 
       role="button">Mahasiswa</a>
       
    <a href="{{ route('matakuliah.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('matakuliah*')) active @endif" 
       role="button">Matakuliah</a>
       
    <a href="{{ route('jadwal.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('jadwal*')) active @endif" 
       role="button">Jadwal</a>
       
    <a href="{{ route('kelas.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('kelas*')) active @endif" 
       role="button">Kelas</a>
       
    <a href="{{ route('krs.index') }}" 
       class="btn nav-btn px-5 py-2 m-2 @if(request()->is('krs*')) active @endif" 
       role="button">KRS</a>
  </div>
</div>