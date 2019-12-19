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
        `providerId`=:providerId
    )
LIMIT
    :startingIndex, :recordLimit