UPDATE
    `session`
SET
    `sessionActivity`=:sessionActivity,
    `sessionData`=:sessionData
WHERE
    `sessionCode`=:sessionCode