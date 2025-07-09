// scanner.js - Enhanced QR Code and barcode scanning logic

document.addEventListener("DOMContentLoaded", () => {
  const barcodeField = document.getElementById("data_barcode")
  const tingkatField = document.getElementById("tingkat_input")
  const tingkatSelect = document.getElementById("tingkat")
  const studentInfo = document.getElementById("student-info")
  const btnSimpan = document.getElementById("btn-simpan")
  const scanStatus = document.getElementById("scan-status")
  const readerDiv = document.getElementById("reader")

  let scanner = null
  let isScanning = false

  // Update tingkat when selected
  tingkatSelect.addEventListener("change", function () {
    tingkatField.value = this.value
    if (this.value) {
      startScanner()
    } else {
      stopScanner()
    }
  })

  function startScanner() {
    if (isScanning) return

    readerDiv.innerHTML = '<p style="color: #007bff;">Memulai kamera...</p>'

    const Html5Qrcode = window.Html5Qrcode
    scanner = new Html5Qrcode("reader")

    Html5Qrcode.getCameras()
      .then((devices) => {
        if (devices && devices.length) {
          isScanning = true
          scanner
            .start(
              devices[0].id,
              {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                supportedScanTypes: ["qr_code", "barcode"],
              },
              onScanSuccess,
              onScanFailure,
            )
            .then(() => {
              showScanStatus("Kamera aktif. Arahkan ke QR Code atau barcode mahasiswa.", "info")
            })
            .catch((err) => {
              showScanStatus("Gagal memulai kamera: " + err, "error")
              isScanning = false
            })
        } else {
          showScanStatus("Tidak ada kamera yang tersedia", "error")
        }
      })
      .catch((err) => {
        showScanStatus("Error mengakses kamera: " + err, "error")
      })
  }

  function stopScanner() {
    if (scanner && isScanning) {
      scanner.stop().then(() => {
        isScanning = false
        readerDiv.innerHTML = '<p style="color: #6c757d;">Pilih tingkat terlebih dahulu untuk memulai scan</p>'
        hideStudentInfo()
      })
    }
  }

  function onScanSuccess(decodedText, decodedResult) {
    console.log("Scanned data:", decodedText)

    // Parse QR Code/barcode data: NIM|Nama|Kelas|Tingkat|Timestamp
    const parts = decodedText.split("|")

    if (parts.length >= 4) {
      const nim = parts[0]
      const nama = parts[1]
      const kelas = parts[2]
      const tingkat = parts[3]
      const timestamp = parts[4] || null

      // Validate tingkat matches selected
      if (tingkat !== tingkatSelect.value) {
        showScanStatus(
          `QR Code tingkat ${tingkat} tidak sesuai dengan tingkat yang dipilih (${tingkatSelect.value})`,
          "error",
        )
        return
      }

      // Validate timestamp if present (check if not too old)
      if (timestamp) {
        const ts = Number.parseInt(timestamp)
        const now = Math.floor(Date.now() / 1000)
        const maxAge = 24 * 60 * 60 // 24 hours

        if (now - ts > maxAge) {
          showScanStatus("QR Code sudah kadaluarsa. Silakan generate QR Code baru.", "error")
          return
        }
      }

      // Check if already scanned today
      checkDuplicateAttendance(nim).then((isDuplicate) => {
        if (isDuplicate) {
          showScanStatus(`Mahasiswa ${nama} (${nim}) sudah melakukan presensi hari ini`, "warning")
          return
        }

        // Fill form data
        barcodeField.value = decodedText
        document.getElementById("display-nim").textContent = nim
        document.getElementById("display-nama").textContent = nama
        document.getElementById("display-kelas").textContent = kelas
        document.getElementById("display-tingkat").textContent = tingkat

        showStudentInfo()
        showScanStatus(`Berhasil scan QR Code: ${nama} (${nim})`, "success")

        // Enable save button
        btnSimpan.disabled = false
        btnSimpan.style.cursor = "pointer"
        btnSimpan.style.background = "#28a745"

        // Auto-focus on status selection
        const statusRadio = document.querySelector('input[name="status"]')
        if (statusRadio) statusRadio.focus()

        // Stop scanner after successful scan
        stopScanner()

        // Play success sound (optional)
        playSuccessSound()
      })
    } else {
      showScanStatus("Format QR Code tidak valid", "error")
    }
  }

  function onScanFailure(error) {
    // Silent fail for continuous scanning
    // Only log significant errors
    if (error && !error.includes("No MultiFormat Readers")) {
      console.debug("Scan error:", error)
    }
  }

  function showStudentInfo() {
    studentInfo.style.display = "block"
    studentInfo.style.animation = "fadeIn 0.3s ease-in"
  }

  function hideStudentInfo() {
    studentInfo.style.display = "none"
    btnSimpan.disabled = true
    btnSimpan.style.cursor = "not-allowed"
    btnSimpan.style.background = "#6c757d"
  }

  function showScanStatus(message, type) {
    const colors = {
      success: { bg: "#d4edda", border: "#c3e6cb", text: "#155724", icon: "check-circle" },
      error: { bg: "#f8d7da", border: "#f5c6cb", text: "#721c24", icon: "exclamation-triangle" },
      warning: { bg: "#fff3cd", border: "#ffeaa7", text: "#856404", icon: "exclamation-triangle" },
      info: { bg: "#d1ecf1", border: "#bee5eb", text: "#0c5460", icon: "info-circle" },
    }

    const color = colors[type] || colors.info

    scanStatus.innerHTML = `<i class="fas fa-${color.icon}"></i> ${message}`
    scanStatus.style.display = "block"
    scanStatus.style.background = color.bg
    scanStatus.style.border = `1px solid ${color.border}`
    scanStatus.style.color = color.text
    scanStatus.style.padding = "12px"
    scanStatus.style.borderRadius = "5px"
    scanStatus.style.marginTop = "15px"

    // Auto hide after 5 seconds for non-error messages
    if (type !== "error") {
      setTimeout(() => {
        scanStatus.style.display = "none"
      }, 5000)
    }
  }

  async function checkDuplicateAttendance(nim) {
    try {
      const response = await fetch("../../controllers/AbsensiController.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=check_duplicate&nim=${nim}&tanggal=${new Date().toISOString().split("T")[0]}`,
      })

      const result = await response.json()
      return result.exists || false
    } catch (error) {
      console.error("Error checking duplicate:", error)
      return false
    }
  }

  // Play success sound
  function playSuccessSound() {
    try {
      // Create audio context for success beep
      const audioContext = new (window.AudioContext || window.webkitAudioContext)()
      const oscillator = audioContext.createOscillator()
      const gainNode = audioContext.createGain()

      oscillator.connect(gainNode)
      gainNode.connect(audioContext.destination)

      oscillator.frequency.value = 800
      oscillator.type = "sine"
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime)
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3)

      oscillator.start(audioContext.currentTime)
      oscillator.stop(audioContext.currentTime + 0.3)
    } catch (error) {
      console.log("Could not play success sound:", error)
    }
  }

  // Update statistics periodically
  function updateStatistics() {
    fetch("../../controllers/AbsensiController.php?action=get_stats&tanggal=" + new Date().toISOString().split("T")[0])
      .then((response) => response.json())
      .then((data) => {
        document.getElementById("count-tepat-waktu").textContent = data.tepat_waktu || 0
        document.getElementById("count-terlambat").textContent = data.terlambat || 0
        document.getElementById("count-tidak-hadir").textContent = data.tidak_hadir || 0
      })
      .catch((error) => console.error("Error updating stats:", error))
  }

  // Update stats every 30 seconds
  updateStatistics()
  setInterval(updateStatistics, 30000)

  // Form submission handler
  document.getElementById("formPresensi").addEventListener("submit", function (e) {
    e.preventDefault()

    const formData = new FormData(this)

    // Show loading state
    btnSimpan.disabled = true
    btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'

    fetch("../../controllers/AbsensiController.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showScanStatus("Presensi berhasil disimpan!", "success")
          this.reset()
          hideStudentInfo()
          updateStatistics()

          // Reset button
          btnSimpan.innerHTML = '<i class="fas fa-save"></i> Simpan Presensi'

          // Restart scanner after 2 seconds
          setTimeout(() => {
            if (tingkatSelect.value) {
              startScanner()
            }
          }, 2000)
        } else {
          showScanStatus("Gagal menyimpan presensi: " + (data.message || "Unknown error"), "error")
          btnSimpan.disabled = false
          btnSimpan.innerHTML = '<i class="fas fa-save"></i> Simpan Presensi'
        }
      })
      .catch((error) => {
        showScanStatus("Error: " + error.message, "error")
        btnSimpan.disabled = false
        btnSimpan.innerHTML = '<i class="fas fa-save"></i> Simpan Presensi'
      })
  })

  // Keyboard shortcuts
  document.addEventListener("keydown", (e) => {
    // Press 'R' to refresh scanner
    if (e.key === "r" || e.key === "R") {
      if (e.ctrlKey || e.metaKey) return // Don't interfere with Ctrl+R

      e.preventDefault()
      if (tingkatSelect.value) {
        stopScanner()
        setTimeout(startScanner, 500)
        showScanStatus("Scanner di-refresh", "info")
      }
    }

    // Press 'Escape' to stop scanner
    if (e.key === "Escape") {
      stopScanner()
      showScanStatus("Scanner dihentikan", "info")
    }
  })
})

// CSS Animation
const style = document.createElement("style")
style.textContent = `
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
  }
  
  .scan-success {
    animation: pulse 0.3s ease-in-out;
  }
  
  #reader {
    border-radius: 10px;
    overflow: hidden;
  }
  
  #reader video {
    border-radius: 10px;
  }
`
document.head.appendChild(style)
