document.addEventListener("DOMContentLoaded", () => {
  const qrData = document.getElementById("qr-data")?.value
  const studentDataEl = document.getElementById("student-data")

  if (!qrData || !studentDataEl) {
    console.error("Required data not found")
    return
  }

  const studentData = JSON.parse(studentDataEl.value)

  // Generate QR Code immediately
  generateQRCode(qrData)

  // Attach event listeners
  document.getElementById("btn-print")?.addEventListener("click", () => printQRCode(studentData))
  document.getElementById("btn-download")?.addEventListener("click", () => downloadQRCode(studentData))
  document.getElementById("btn-refresh")?.addEventListener("click", refreshQRCode)
})

function generateQRCode(data) {
  const container = document.getElementById("qrcode")
  const QRCode = window.QRCode // Declare the QRCode variable here
  if (!container || !QRCode) {
    setTimeout(() => generateQRCode(data), 100)
    return
  }

  container.innerHTML = ""

  QRCode.toCanvas(
    data,
    {
      width: 200,
      height: 200,
      margin: 1,
      color: { dark: "#000000", light: "#FFFFFF" },
      errorCorrectionLevel: "L",
    },
    (error, canvas) => {
      if (error) {
        container.innerHTML = '<div class="error">Failed to generate QR code</div>'
        return
      }

      canvas.className = "qr-canvas"
      container.appendChild(canvas)
    },
  )
}

function printQRCode(studentData) {
  const canvas = document.querySelector("#qrcode canvas")
  if (!canvas) {
    showToast("QR Code not ready", "error")
    return
  }

  const printWindow = window.open("", "_blank")
  const dataURL = canvas.toDataURL()

  printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code - ${studentData.nama}</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 20px; margin: 0; }
                .header { margin-bottom: 30px; }
                .info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; display: inline-block; }
                .info-row { display: flex; justify-content: space-between; margin: 8px 0; min-width: 300px; }
                .info-row strong { margin-right: 20px; }
                img { border: 2px solid #000; border-radius: 8px; margin: 20px 0; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>QR Code Absensi Apel Tingkat</h2>
                <p>Politeknik Negeri Malang</p>
            </div>
            <div class="info">
                <div class="info-row"><strong>NIM:</strong> <span>${studentData.nim}</span></div>
                <div class="info-row"><strong>Nama:</strong> <span>${studentData.nama}</span></div>
                <div class="info-row"><strong>Kelas:</strong> <span>${studentData.kelas}</span></div>
                <div class="info-row"><strong>Tingkat:</strong> <span>${studentData.tingkat}</span></div>
            </div>
            <img src="${dataURL}" alt="QR Code" style="width: 250px; height: 250px;">
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() { window.close(); }
                }
            </script>
        </body>
        </html>
    `)

  printWindow.document.close()
  showToast("Print dialog opened", "success")
}

function downloadQRCode(studentData) {
  const canvas = document.querySelector("#qrcode canvas")
  if (!canvas) {
    showToast("QR Code not ready", "error")
    return
  }

  const link = document.createElement("a")
  link.download = `qrcode_${studentData.nim}_${new Date().toISOString().slice(0, 10)}.png`
  link.href = canvas.toDataURL()

  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)

  showToast("QR Code downloaded!", "success")
}

function refreshQRCode() {
  showToast("Refreshing...", "info")
  setTimeout(() => location.reload(), 500)
}

function showToast(message, type) {
  const toast = document.createElement("div")
  toast.className = `toast toast-${type}`
  toast.textContent = message

  document.body.appendChild(toast)

  setTimeout(() => toast.classList.add("show"), 100)
  setTimeout(() => {
    toast.classList.remove("show")
    setTimeout(() => document.body.removeChild(toast), 300)
  }, 3000)
}
