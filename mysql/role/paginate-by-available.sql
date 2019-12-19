SELECT
    `roleId`,
    `roleName`,
    `roleDescription`,
    `roleFlags`,
    `providerId`
FROM
    `role`
WHERE
    (
        `providerId`=0 OR
        `providerId`=:providerId
    )
LIMIT
    :startingIndex, :recordLimit