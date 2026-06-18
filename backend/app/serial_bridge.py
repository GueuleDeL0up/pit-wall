import serial


class SerialBridge:
    """Liaison série avec le STM32 (protocole texte ligne par ligne)."""

    def __init__(self, port: str, baudrate: int = 9600):
        self.port = port
        self.baudrate = baudrate
        self._serial: serial.Serial | None = None

    def connect(self) -> None:
        self._serial = serial.Serial(self.port, self.baudrate, timeout=1)

    def close(self) -> None:
        if self._serial is not None:
            self._serial.close()
            self._serial = None

    def read_line(self) -> str | None:
        if self._serial is None:
            return None
        line = self._serial.readline().decode("utf-8", errors="ignore").strip()
        return line or None

    def send_command(self, command: str) -> None:
        if self._serial is None:
            return
        self._serial.write(f"{command}\n".encode("utf-8"))
