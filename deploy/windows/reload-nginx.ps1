param(
    [string]$NginxRoot = 'C:\nginx'
)

$nginxExe = Join-Path $NginxRoot 'nginx.exe'
$exitCode = 0

if (-not (Test-Path -LiteralPath $nginxExe)) {
    throw "nginx.exe bulunamadi: $nginxExe"
}

Push-Location $NginxRoot
try {
    & $nginxExe -t
    if ($LASTEXITCODE -ne 0) {
        $exitCode = $LASTEXITCODE
        return
    }

    & $nginxExe -s reload
    $exitCode = $LASTEXITCODE
}
finally {
    Pop-Location
}

exit $exitCode
