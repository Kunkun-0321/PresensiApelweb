// barcode.js - Optimized and lightweight version

;(() => {
  // Initialize scanner if on scanner page
  if (document.getElementById("reader")) {
    initializeScanner()
  }

  function initializeScanner() {
    const elements = {
      barcodeField: document.getElementById("data_barcode"),
      tingkatField: document.getElementById("tingkat_input"),
      tingkatSelect: document.getElementById("tingkat"),
      studentInfo: document.getElementById("student-info"),
      btnSimpan: document.getElementById("btn-simpan"),
      scanStatus: document.getElementById("scan-status"),
      readerDiv: document.getElementById("reader"),
    }

    let scanner = null
    let isScanning = false

    // Update tingkat when selected
    if (elements.tingkatSelect) {
      elements.tingkatSelect.addEventListener("change", function () {
        if (elements.tingkatField) elements.tingkatField.value = this.value
        if (this.value) {
          startScanner()
        } else {
          stopScanner()
        }
      })
    }

    function startScanner() {
      if (isScanning || typeof window.Html5Qrcode === "undefined") return

      if (elements.readerDiv) {
        elements.readerDiv.innerHTML = '<p style="color: #007bff;">Starting camera...</p>'
      }

      scanner = new window.Html5Qrcode("reader")

      window.Html5Qrcode.getCameras()
        .then((devices) => {
          if (devices && devices.length) {
            isScanning = true
            return scanner.start(
              devices[0].id,
              { fps: 10, qrbox: { width: 250, height: 250 } },
              onScanSuccess,
              () => {}, // Silent error handling
            )
          }
        })
        .then(() => {
          showScanStatus("Camera active. Point to QR Code.", "info")
        })
        .catch((err) => {
          showScanStatus("Camera error: " + err, "error")
          isScanning = false
        })
    }

    function stopScanner() {
      if (scanner && isScanning) {
        scanner.stop().then(() => {
          isScanning = false
          if (elements.readerDiv) {
            elements.readerDiv.innerHTML = '<p style="color: #6c757d;">Select level first</p>'
          }
          hideStudentInfo()
        })
      }
    }

    function onScanSuccess(decodedText) {
      const parts = decodedText.split("|")

      if (parts.length >= 4) {
        const [nim, nama, kelas, tingkat, timestamp] = parts

        // Validate tingkat
        if (elements.tingkatSelect && tingkat !== elements.tingkatSelect.value) {
          showScanStatus(`Wrong level: ${tingkat} vs ${elements.tingkatSelect.value}`, "error")
          return
        }

        // Validate timestamp (24 hours)
        if (timestamp) {
          const ts = Number.parseInt(timestamp)
          const now = Math.floor(Date.now() / 1000)
          if (now - ts > 86400) {
            // 24 hours
            showScanStatus("QR Code expired. Generate new one.", "error")
            return
          }
        }

        // Fill form data
        if (elements.barcodeField) elements.barcodeField.value = decodedText

        const displayElements = ["nim", "nama", "kelas", "tingkat"]
        const values = [nim, nama, kelas, tingkat]

        displayElements.forEach((field, index) => {
          const element = document.getElementById(`display-${field}`)
          if (element) element.textContent = values[index]
        })

        showStudentInfo()
        showScanStatus(`Scanned: ${nama} (${nim})`, "success")

        // Enable save button
        if (elements.btnSimpan) {
          elements.btnSimpan.disabled = false
          elements.btnSimpan.style.cursor = "pointer"
          elements.btnSimpan.style.background = "#28a745"
        }

        stopScanner()
      } else {
        showScanStatus("Invalid QR Code format", "error")
      }
    }

    function showStudentInfo() {
      if (elements.studentInfo) {
        elements.studentInfo.style.display = "block"
      }
    }

    function hideStudentInfo() {
      if (elements.studentInfo) {
        elements.studentInfo.style.display = "none"
      }
      if (elements.btnSimpan) {
        elements.btnSimpan.disabled = true
        elements.btnSimpan.style.cursor = "not-allowed"
        elements.btnSimpan.style.background = "#6c757d"
      }
    }

    function showScanStatus(message, type) {
      if (!elements.scanStatus) return

      const colors = {
        success: { bg: "#d4edda", text: "#155724" },
        error: { bg: "#f8d7da", text: "#721c24" },
        warning: { bg: "#fff3cd", text: "#856404" },
        info: { bg: "#d1ecf1", text: "#0c5460" },
      }

      const color = colors[type] || colors.info

      elements.scanStatus.innerHTML = message
      elements.scanStatus.style.display = "block"
      elements.scanStatus.style.background = color.bg
      elements.scanStatus.style.color = color.text
      elements.scanStatus.style.padding = "10px"
      elements.scanStatus.style.borderRadius = "5px"
      elements.scanStatus.style.marginTop = "10px"

      if (type !== "error") {
        setTimeout(() => {
          elements.scanStatus.style.display = "none"
        }, 5000)
      }
    }

    // Form submission
    const formPresensi = document.getElementById("formPresensi")
    if (formPresensi) {
      formPresensi.addEventListener("submit", function (e) {
        e.preventDefault()

        const formData = new FormData(this)

        if (elements.btnSimpan) {
          elements.btnSimpan.disabled = true
          elements.btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...'
        }

        fetch("../../controllers/AbsensiController.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showScanStatus("Attendance saved successfully!", "success")
              this.reset()
              hideStudentInfo()

              if (elements.btnSimpan) {
                elements.btnSimpan.innerHTML = '<i class="fas fa-save"></i> Save Attendance'
              }

              // Restart scanner
              setTimeout(() => {
                if (elements.tingkatSelect && elements.tingkatSelect.value) {
                  startScanner()
                }
              }, 2000)
            } else {
              showScanStatus("Failed to save: " + (data.message || "Unknown error"), "error")
              if (elements.btnSimpan) {
                elements.btnSimpan.disabled = false
                elements.btnSimpan.innerHTML = '<i class="fas fa-save"></i> Save Attendance'
              }
            }
          })
          .catch((error) => {
            showScanStatus("Error: " + error.message, "error")
            if (elements.btnSimpan) {
              elements.btnSimpan.disabled = false
              elements.btnSimpan.innerHTML = '<i class="fas fa-save"></i> Save Attendance'
            }
          })
      })
    }

    // Update statistics
    function updateStats() {
      const today = new Date().toISOString().split("T")[0]
      fetch(`../../controllers/AbsensiController.php?action=get_stats&tanggal=${today}`)
        .then((response) => response.json())
        .then((data) => {
          const stats = {
            "count-tepat-waktu": data.tepat_waktu || 0,
            "count-terlambat": data.terlambat || 0,
            "count-tidak-hadir": data.tidak_hadir || 0,
          }

          Object.entries(stats).forEach(([id, value]) => {
            const element = document.getElementById(id)
            if (element) element.textContent = value
          })
        })
        .catch(() => {}) // Silent error
    }

    // Update stats every 30 seconds
    updateStats()
    setInterval(updateStats, 30000)
  }
})()
