import { Card, Flex, Text } from "@tremor/react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCheckSquare } from "@fortawesome/free-solid-svg-icons";

export default function UpdateQuiltCheckedInStatus({
    formData,
    setFormData,
    checkedIn,
    setCheckedIn,
    quiltId,
    onUpdate,
}) {
    async function handleChange() {
        const newCheckedInStatus = !checkedIn;
        setCheckedIn(newCheckedInStatus);
        setFormData({
            ...formData,
            checkedIn: newCheckedInStatus,
        });

        try {
            const response = await fetch(`/api/updateQuiltStatus/${quiltId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    checkedIn: newCheckedInStatus,
                }),
            });

            if (response.ok) {
                onUpdate();
            } else {
                console.error("Failed to update quilt status");
            }
        } catch (error) {
            console.error("Error updating quilt status:", error);
        }
    }

    return (
        <div
            className={`flex items-center space-x-2 bg-white text-gray-700 px-4 py-2 rounded-lg cursor-pointer`}
            onClick={handleChange}
        >
            {checkedIn && (
                <FontAwesomeIcon
                    icon={faCheckSquare}
                    className="h-4 w-4 rounded border border-gray-500 bg-blue-500 text-white focus:ring-2 focus:ring-blue-300"
                />
            )}
            {!checkedIn && (
                <div className="h-4 w-4 rounded border border-gray-500 bg-white text-gray-700 focus:ring-2 focus:ring-blue-300" />
            )}
            <label htmlFor="CheckInQuilt" className="text-sm ml-2 font-medium">
                Check In Quilt
            </label>
        </div>
    );
}
