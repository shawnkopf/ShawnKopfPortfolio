import { Fragment, useEffect, useRef, useState } from "react";
import { Dialog, Transition } from "@headlessui/react";
import {
    Button,
    Card,
    DatePicker,
    Flex,
    Grid,
    Select,
    SelectItem,
    Text,
} from "@tremor/react";
import { SearchSelect, SearchSelectItem } from "@tremor/react";
import { RefreshIcon } from "@heroicons/react/outline";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCamera, faFloppyDisk } from "@fortawesome/free-solid-svg-icons";
import Resizer from "react-image-file-resizer";
import BarcodeScanner from "@/Components/BarcodeScanner.jsx";
import { isMobile } from "react-device-detect";
import UpdateCommunication from "@/Components/QuiltUpdate/UpdateCommunication.jsx";
import UpdateDetails from "@/Components/QuiltUpdate/UpdateDetails.jsx";
import UpdateImg from "@/Components/QuiltUpdate/UpdateImg.jsx";

export default function QuiltUpdateModal({
    auth,
    openModal,
    setOpenModal,
    user,
    quilt,
    setQuilt,
    emailCopies,
}) {
    const [open, setOpen] = useState(openModal);
    const [loading, setLoading] = useState(false);
    const [sendEmail, setSendEmail] = useState(false);
    const [checkedIn, setCheckedIn] = useState(quilt.checkedIn || false);
    const [img, setImg] = useState(null);
    const [scannerOpen, setScannerOpen] = useState(false);
    const [formData, setFormData] = useState({
        status: "",
        location: "",
        updatedBy: auth.user.id,
        statusChangeDate: new Date().toISOString().slice(0, 10),
        internalNotes: "",
        emailCopy: "",
        photo: "none",
        sendEmail: false,
    });

    const [notification, setNotification] = useState(null);

    useEffect(() => {
        if (emailCopies[formData.status.toLowerCase()]) {
            setFormData({
                ...formData,
                emailCopy: emailCopies[formData.status.toLowerCase()],
            });
        } else {
            setFormData({ ...formData, emailCopy: emailCopies.empty });
        }
    }, [formData.status]);

    useEffect(() => {
        setOpen(openModal);
    }, [openModal]);

    useEffect(() => {
        setFormData({ ...formData, photo: img });
    }, [img]);

    function handleChange(e) {
        setFormData({
            ...formData,
            [e.target.id]: e.target.value,
        });
    }

    function handleStatusChange(e) {
        setFormData({
            ...formData,
            status: e,
        });
    }

    useEffect(() => {
        setFormData({ ...formData, sendEmail: sendEmail });
    }, [sendEmail]);

    useEffect(() => {
        setFormData({ ...formData, checkedIn: checkedIn });
    }, [checkedIn]);

    useEffect(() => {
        setFormData({ ...formData, photo: img });
    }, [img]);

    function handleSendEmail() {
        if (sendEmail) {
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    copy: formData.emailCopy,
                    status: formData.status,
                    quilt: quilt,
                    photo: formData.photo,
                }),
            };
            fetch("/api/sendQuiltUpdateEmail", requestOptions).then(() =>
                console.log("Email Sent")
            );
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);
        const url = "/api/" + quilt.id + "/update";
        const requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData),
        };

        handleSendEmail();

        fetch(url, requestOptions)
            .then((response) => response.json())
            .then((data) => {
                setQuilt(data);
                if (checkedIn) {
                    const storedQuilts =
                        JSON.parse(localStorage.getItem("printedQuilts")) || [];
                    const updatedQuilt = {
                        id: quilt.id,
                        customerName: data.customerName,
                        orderName: data.orderName,
                        dueDate: data.dueDate,
                        ...quilt,
                    };

                    const alreadyCheckedIn = storedQuilts.some(
                        (q) => q.orderName === updatedQuilt.orderName
                    );

                    if (alreadyCheckedIn) {
                        setNotification(
                            "Quilt with this order number has already been checked in!"
                        );
                    } else {
                        if (storedQuilts.length >= 5) {
                            setNotification(
                                "Cannot add more than 5 quilts to the print queue!"
                            );
                        } else {
                            storedQuilts.push(updatedQuilt);
                            localStorage.setItem(
                                "printedQuilts",
                                JSON.stringify(storedQuilts)
                            );
                        }
                    }
                }
            })
            .then(() => setOpenModal(false))
            .then(() => setLoading(false));
    }
    function handleCheckInChange() {
        setCheckedIn(!checkedIn);

        const storedQuilts =
            JSON.parse(localStorage.getItem("printedQuilts")) || [];
        const alreadyCheckedIn = storedQuilts.some(
            (q) => q.orderName === quilt.orderName
        );

        if (alreadyCheckedIn) {
            setNotification(
                "Quilt with this order number has already been checked in!"
            );
        } else if (storedQuilts.length >= 5) {
            setNotification(
                "Cannot add more than 5 quilts to the print queue!"
            );
        } else {
            setNotification(null);
        }
    }

    const cancelButtonRef = useRef(null);

    return (
        <Transition.Root show={open} as={Fragment}>
            <Dialog
                as="div"
                className="relative z-10"
                initialFocus={cancelButtonRef}
                onClose={setOpenModal}
            >
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </Transition.Child>
                <div className="fixed inset-0 z-10 overflow-y-auto">
                    <div className="flex min-h-full items-end justify-center p-4 text-center lg:items-center sm:p-0">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enterTo="opacity-100 translate-y-0 sm:scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                            leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <Dialog.Panel className="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                <div className="bg-white px-6 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div className="sm:flex sm:items-start">
                                        <div className="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"></div>
                                        <div className="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                            <Dialog.Title
                                                as="h3"
                                                className="text-base font-semibold leading-6 text-gray-900"
                                            >
                                                Update Quilt Status
                                            </Dialog.Title>
                                        </div>
                                    </div>
                                    <Grid numItemsMd={1} className="gap-6 mt-6">
                                        <UpdateDetails
                                            formData={formData}
                                            handleStatusChange={
                                                handleStatusChange
                                            }
                                            scannerOpen={scannerOpen}
                                            handleChange={handleChange}
                                            img={img}
                                            handleSubmit={handleSubmit}
                                            user={user}
                                            loading={loading}
                                        />
                                        <UpdateCommunication
                                            formData={formData}
                                            setFormData={setFormData}
                                            sendEmail={sendEmail}
                                            setSendEmail={setSendEmail}
                                        />
                                    </Grid>
                                    <Grid className="mt-6">
                                        <UpdateImg
                                            img={img}
                                            setImg={setImg}
                                            formData={formData}
                                            setFormData={setFormData}
                                        />
                                    </Grid>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 items-center">
                                    <div className="flex items-center gap-2 mb-2 sm:mb-0 sm:mr-auto">
                                        <label
                                            htmlFor="checkedIn"
                                            className="flex gap-2"
                                        >
                                            <input
                                                type="checkbox"
                                                id="checkedIn"
                                                name="checkedIn"
                                                checked={checkedIn}
                                                onChange={handleCheckInChange}
                                                className="h-5 w-5 rounded-md border-gray-200 bg-white shadow-sm"
                                            />
                                            Check-In Quilt
                                        </label>
                                    </div>
                                    <div className="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 grid-cols-2">
                                        <div>
                                            {notification && (
                                                <p className="ml-2 text-sm text-red-500">
                                                    {notification}
                                                </p>
                                            )}
                                            {!sendEmail && (
                                                <p className=" ml-2 text-sm text-red-500">
                                                    Not Sending Email!
                                                </p>
                                            )}

                                            {!img && (
                                                <p className=" ml-2 text-sm text-gray-500">
                                                    No Photo!
                                                </p>
                                            )}

                                            {!formData.location && (
                                                <p className=" ml-2 text-sm text-red-500">
                                                    Location Required!
                                                </p>
                                            )}

                                            {!formData.internalNotes && (
                                                <p className=" ml-2 text-sm text-gray-500">
                                                    Internal Notes Required!
                                                </p>
                                            )}
                                        </div>

                                        <Flex
                                            justifyContent="end"
                                            className="space-x-2 border-t pt-4"
                                        >
                                            <Button
                                                size="xs"
                                                variant="secondary"
                                                color="red"
                                                onClick={() =>
                                                    setOpenModal(false)
                                                }
                                            >
                                                Cancel
                                            </Button>

                                            <Button
                                                size="xs"
                                                loading={loading}
                                                variant="primary"
                                                onClick={(e) => handleSubmit(e)}
                                                disabled={
                                                    loading ||
                                                    formData.location === "" ||
                                                    formData.status === "" ||
                                                    formData.internalNotes ===
                                                        ""
                                                }
                                            >
                                                {!loading && (
                                                    <FontAwesomeIcon
                                                        icon={faFloppyDisk}
                                                        beat
                                                    />
                                                )}{" "}
                                                Save Update
                                            </Button>
                                        </Flex>
                                    </div>
                                </div>
                            </Dialog.Panel>
                        </Transition.Child>
                    </div>
                </div>
            </Dialog>
        </Transition.Root>
    );
}
