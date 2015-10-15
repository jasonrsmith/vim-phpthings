if !exists("g:phpthings_plugindir")
  let g:phpthings_plugindir = expand('<sfile>:p:h:h')
endif

function! PhpGetNamespace()
  let filename = system(g:phpthings_plugindir . '/scripts/getnamespace.php  ' . bufname("%") . ' ' . getcwd())
  call append(0, split(filename, "\n"))
endfunction
command! PhpGetNamespace call PhpGetNamespace()

function! SetupLeaders()
  nnoremap <localleader>n :call PhpGetNamespace()<cr>
endfunction

au! BufEnter *.php call SetupLeaders()
