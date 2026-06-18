from pydantic import BaseModel


class LedCommand(BaseModel):
    etat: bool
