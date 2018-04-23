PendingAction states
--------------------

  * `STATE_WAITING` (0) : The action is waiting to be processed.
  * `STATE_PROCESSING` (1) : The action is being processed.
  * `STATE_PROCESSED` (2) : The action is processed.
  * `STATE_ERROR` (3) : An error occured during the process or during the check of the action.
  * `STATE_UNKNOWN_HANDLER` (4) : The handler is not registered or not found.
  * `STATE_HANDLER_ERROR` (5) : The handler does not implements the `HandlerInterface`.
