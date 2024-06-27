import {
    Button,
    Card,
    Flex,
    SearchSelect,
    SearchSelectItem,
    Text,
} from "@tremor/react";
import BarcodeScanner from "@/Components/BarcodeScanner.jsx";
import { isMobile } from "react-device-detect";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCamera, faFloppyDisk } from "@fortawesome/free-solid-svg-icons";

export default function UpdateDetails({
    formData,
    handleStatusChange,
    scannerOpen,
    handleChange,
    img,
    handleSubmit,
    user,
    loading,
}) {
    return (
        <div className="">
            <Flex alignItems="start">
                <div>
                    <Text>Update Details</Text>
                </div>
            </Flex>
            <form action="" className="space-y-4 mt-6">
                <div>
                    <SearchSelect
                        id="status"
                        value={formData.status}
                        onValueChange={(e) => handleStatusChange(e)}
                    >
                        <SearchSelectItem value="received">
                            Received
                        </SearchSelectItem>
                        <SearchSelectItem value="staging">
                            Staging
                        </SearchSelectItem>
                        <SearchSelectItem value="staged">
                            Staged
                        </SearchSelectItem>
                        <SearchSelectItem value="quilting">
                            Quilting
                        </SearchSelectItem>
                        <SearchSelectItem value="quilted">
                            Quilted
                        </SearchSelectItem>
                        <SearchSelectItem value="trimming">
                            Trimming
                        </SearchSelectItem>
                        <SearchSelectItem value="trimmed">
                            Trimmed
                        </SearchSelectItem>
                        <SearchSelectItem value="binding">
                            Binding
                        </SearchSelectItem>
                        <SearchSelectItem value="bound">Bound</SearchSelectItem>
                        <SearchSelectItem value="shipping">
                            Shipping
                        </SearchSelectItem>
                        <SearchSelectItem value="shipped">
                            Shipped
                        </SearchSelectItem>
                        <SearchSelectItem value="cancelled">
                            Cancelled
                        </SearchSelectItem>
                    </SearchSelect>
                </div>
                <div>
                    {scannerOpen && (
                        <BarcodeScanner
                            scanType="input"
                            setStateFunction={setFormData}
                            oldState={formData}
                            setScannerOpen={setScannerOpen}
                        />
                    )}
                    <label className="" htmlFor="location">
                        Location{" "}
                        <span className="text-sm font-semibold text-gray-400">
                            (required)
                        </span>
                    </label>
                    <div className="relative">
                        <input
                            placeholder="Location"
                            type="text"
                            id="location"
                            onChange={(e) => handleChange(e)}
                            value={formData.location}
                            required
                            className="block w-full rounded-lg border-gray-200 p-3 text-sm"
                        />
                        <div
                            className="absolute right-3.5 bottom-2.5"
                            role="group"
                        >
                            {isMobile && (
                                <button
                                    type="button"
                                    className="inline-block rounded-l px-6 pb-[6px] pt-2 text-xs font-medium uppercase leading-normal text-primary transition duration-150 ease-in-out hover:border-primary-600 hover:bg-neutral-500 hover:bg-opacity-10 hover:text-primary-600 focus:border-primary-600 focus:text-primary-600 focus:outline-none focus:ring-0 active:border-primary-700 active:text-primary-700 dark:hover:bg-neutral-100 dark:hover:bg-opacity-10"
                                    data-te-ripple-init
                                    data-te-ripple-color="light"
                                    onClick={() => setScannerOpen(!scannerOpen)}
                                >
                                    <FontAwesomeIcon icon={faCamera} />
                                </button>
                            )}
                        </div>
                    </div>

                    {/*<input*/}
                    {/*    className="w-full rounded-lg border-gray-200 p-3 text-sm"*/}
                    {/*    placeholder="Location"*/}
                    {/*    type="text"*/}
                    {/*    id="location"*/}
                    {/*    onChange={(e) => handleChange(e)}*/}
                    {/*    value={formData.location}*/}
                    {/*    required*/}
                    {/*/>*/}
                </div>
                <div>
                    <label className="" htmlFor="updatedBy">
                        Updated By
                    </label>
                    <input
                        className="w-full rounded-lg border-gray-200 p-3 text-sm"
                        placeholder={user.name}
                        type="text"
                        id="updatedBy"
                        disabled
                    />
                </div>
                <div>
                    <label className="" htmlFor="statusChangeDate">
                        Status Change Date
                    </label>
                    <input
                        className="w-full rounded-lg border-gray-200 p-3 text-sm"
                        type="date"
                        id="statusChangeDate"
                        value={formData.statusChangeDate}
                        onChange={(e) => handleChange(e)}
                    />
                </div>
                <div>
                    <label className="" htmlFor="internalNotes">
                        Internal Notes{" "}
                        <span className="text-sm font-semibold text-gray-400">
                            (required)
                        </span>
                    </label>

                    <textarea
                        className="w-full rounded-lg border-gray-200 p-3 text-sm"
                        placeholder="Add notes here..."
                        rows="4"
                        id="internalNotes"
                        value={formData.internalNotes}
                        onChange={(e) => handleChange(e)}
                    ></textarea>
                </div>
            </form>
        </div>
    );
}
