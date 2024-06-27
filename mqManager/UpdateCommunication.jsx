import { Card, Flex, Text } from "@tremor/react";

export default function UpdateCommunication({
    formData,
    setFormData,
    sendEmail,
    setSendEmail,
}) {
    function handleChange() {
        console.log(sendEmail);
        setSendEmail(!sendEmail);
    }
    return (
        <Card>
            <Flex alignItems="start">
                <div>
                    <Text>Update Email/Text</Text>
                </div>
            </Flex>
            <div className="overflow-hidden rounded-lg border border-gray-200 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 mt-6">
                <textarea
                    id="emailCopy"
                    className="w-full resize-none border-none align-top focus:ring-0 sm:text-sm"
                    rows="8"
                    placeholder="Send a nice note to the customer..."
                    onChange={(e) =>
                        setFormData({
                            ...formData,
                            emailCopy: { email_copy: e.target.value },
                        })
                    }
                    value={formData.emailCopy.email_copy}
                    //disabled={emailSent}
                ></textarea>

                <div className="flex items-center justify-end gap-2 bg-white p-3">
                    <label htmlFor="MarketingAccept" className="flex gap-4">
                        <input
                            type="checkbox"
                            id="MarketingAccept"
                            name="marketing_accept"
                            checked={sendEmail}
                            onChange={handleChange}
                            className="h-5 w-5 rounded-md border-gray-200 bg-white shadow-sm"
                        />

                        <span className="text-sm text-gray-700">
                            Send email on status save
                        </span>
                    </label>
                </div>
            </div>
        </Card>
    );
}
