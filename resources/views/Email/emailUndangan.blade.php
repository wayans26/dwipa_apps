<div>
    <img width='100%' src="{{ 'https://gateway.reg-gemindonesia.net/banner/exhibitions/' . $data['idexhibitions'] }}">
    <p>Dear <b>{{ Str::Upper($data['name']) }}</b></p>
    <p>Thank you for pre-registering to visit {{ $data['keterangan'] }} - Your Online Visitor Pre-registration is
        Confirmed.
        We are pleased to confirm that your registration is
        successful.</p>

    <p><b><u>Collecting Badge</u></b><br /><b>Step 1: Print / save</b> this confirmation email and bring it on the day
        of your visit. Ensure that the QR Code is printed clearly.<br /><b>Step 2: </b> Scan the barcode at Visitor
        <b>Self-Printing Counter</b> and collect your badge.
    </p>
    <br>
    <img style="width: 150px;height: 150px;" src="{{ $data['url'] }}">
    <br>
    <table style='width: 75%;'>
        <tbody>
            <tr>
                <td><b><u>Your Information</u></b></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Company Name</td>
                <td>:</td>
                <td>{{ $data['company'] }}
                </td>
            </tr>
            <tr>
                <td>Visitor Name</td>
                <td>:</td>
                <td>{{ $data['name'] }}
                </td>
            </tr>
            <tr>
                <td>Title</td>
                <td>:</td>
                <td>{{ $data['job_title'] }}
                </td>
            </tr>
            {{-- <tr>
                <td>Address</td>
                <td>:</td>
                <td>{{ $req->address }}
                </td>
            </tr> --}}
            <tr>
                <td>City</td>
                <td>:</td>
                <td>{{ $data['city'] }}
                </td>
            </tr>
            <tr>
                <td>Country</td>
                <td>:</td>
                <td>{{ $data['country'] }}
                </td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td>{{ $data['email'] }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: baseline">Opening Hours</td>
                <td style="vertical-align: baseline">:</td>
                <td>{!! $data['opening_hours'] !!}
                </td>
            </tr>
        </tbody>
    </table><br />
    <p>We look forward to welcoming you at the event.<br />
        <br />Yours sincerely<br /><br />
    <p>GEM Indonesia Team</p>
</div>
