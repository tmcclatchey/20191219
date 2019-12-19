UPDATE
    `assignment`
SET
    `roleId`=:newRole
WHERE
    `roleId`=:oldRole;